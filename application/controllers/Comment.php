<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends MY_Controller
{

    /**
     * Ham khoi dong
     */
    function __construct()
    {
        parent::__construct();

        // Tai cac file thanh phan
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('comment_model');
        $this->load->model('user_model');

        $this->lang->load('site/comment');
    }


    /**
     * Gan dieu kien cho cac bien
     */
    function _set_rules($params = array())
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $rules = array();
        $rules['user'] = array('user', 'callback__check_user');
        $rules['rate'] = array('rate', 'trim|xss_clean|greater_than[0]|less_than[6]');
        $rules['content'] = array('content', 'required|trim|xss_clean|min_length[1]|max_length[255]|filter_html');
        $rules['security_code'] = array('security_code', 'required|trim|callback__check_security_code');
        $rules['parent_id'] = array('parent_id', 'trim|callback__check_parent_id');
        $rules['table_id'] = array('product_id', 'required|trim|callback__check_table_id');
        $rules['table_name'] = array('table_name', 'trim|xss_clean');

        foreach ($params as $param) {
            if (isset($rules[$param])) {
                $this->form_validation->set_rules($param, 'lang:' . $rules[$param][0], $rules[$param][1]);
            }
        }
    }


    /**
     * Kiem tra id comment cha
     */
    function _check_parent_id($value)
    {
        if (!$value) {
            return TRUE;
        }

        $where = array();
        $where['id'] = $value;
        $id = model('comment')->get_id($where);
        if (!$id) {
            $this->form_validation->set_message(__FUNCTION__, lang('notice_value_invalid'));
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Kiem tra id comment cha
     */
    function _check_user($value)
    {
        if (!user_is_login()) {

            $this->form_validation->set_message(__FUNCTION__, 'Bạn cần đăng nhập để sử dụng chức năng này');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Kiem tra id comment cha
     */
    function _check_table_id($value, $type, &$err = null)
    {
        $where = array();
        $where['id'] = $value;
        if ($type == 'product')
            $id = model('product')->get_id($where);
        elseif ($type == 'lesson')
            $id = model('lesson')->get_id($where);
        elseif ($type == 'site') {
            $user = user_get_account_info();
            if (!$user) {
                $err = 'Vui lòng đăng nhập để có thể sử dụng chức năng này';
                return false;
            }
            // kiem tra xem user nay da vote chua
            $err = 'Cám ơn, bạn đã đánh giá rồi.';

            $id = !model("comment")->check_exits(['user_id' => $user->id, 'table_name' => 'site']);
            //$id = 1;
        } else {
            $id = model($type)->get_id($where);
        }

        //pr($id);
        if (!$id) {
            $this->form_validation->set_message(__FUNCTION__, lang('notice_value_invalid'));
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Kiem tra ma bao mat
     */
    function _check_security_code($value)
    {
        $this->load->library('captcha_library');

        if (!$this->captcha_library->check($value, 'four')) {
            $this->form_validation->set_message(__FUNCTION__, lang('notice_value_incorrect'));
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Tu dong kiem tra gia tri cua bien
     */
    function _autocheck($param)
    {
        $this->_set_rules($param);

        $result = array();
        $result['accept'] = $this->form_validation->run();
        $result['error'] = form_error($param);

        $output = json_encode($result);
        set_output('json', $output);
    }


    function add()
    {
        $user = user_get_account_info();

        if (!$user) {
            $result["modal_box"] = "modal-login-require";
            $this->_response($result);
        }
        // if(!mod("product")->setting('comment_allow'))
        // redirect();
        // Tai cac file thanh phan
        // Tu dong kiem tra gia tri cua 1 bien
        $param = $this->input->post('_autocheck');
        if ($param) {
            $this->_autocheck($param);
        }

        //kiem tra id product
        $table_id = $this->input->post('table_id');
        $table_name = $this->input->post('table_name');
        $err = '';
        if (!$this->_check_table_id($table_id, $table_name, $err)) {
            set_output('json', json_encode(['user' => $err]));
        }

        // Gan dieu kien cho cac bien
        $params = array('user', 'content');
        if (in_array($table_name, ['site'/*,'product'*/]))
            $params[] = 'rate';

        $this->_set_rules($params);

        // Xu ly du lieu
        $result = array();
        if ($this->form_validation->run()) {
            // neu la khoa hoc thi chi cho comment 1 lan
            /*if($table_name =="product"){
                if(model("comment")->check_exits(["table_id"=>$table_id,"table_name"=>$table_name,"user_id"=>$user->id]))
                   set_output('json', json_encode(['user' => "Bạn đã đánh giá tin bài này rồi!"]));

            }*/
            // Lay content
            $content = $this->input->post('content');
            $content = strip_tags($content);

            // Them du lieu vao data
            $data = array();
            $data['table_id'] = $table_id;
            $data['table_name'] = $table_name;
            $data['rate'] = floatval($this->input->post('rate'));
            $data['content'] = $content;
            $data['user_id'] = $user->id;
            $comment_active_status = 1;// mod("product")->setting('comment_auto_verify');

            if ($comment_active_status == config('status_on', 'main')) {
                $data['status'] = config('verify_yes', 'main');

                //them so lan nhan xet
                if ($table_name == 'site')
                    $model = (object)setting_get_group('site-rating');
                else
                    $model = model($table_name)->get_info($table_id, 'id, comment_count, rate_total, rate_one, rate_two, rate_three, rate_four, rate_five');

                $_data = array();
                $_data['comment_count'] = $model->comment_count + 1;

                if (isset($data['rate']) && $data['rate']) {
                    $_data['rate_total'] = $model->rate_total + 1;
                    $arrs = array(
                        1 => 'rate_one',
                        2 => 'rate_two',
                        3 => 'rate_three',
                        4 => 'rate_four',
                        5 => 'rate_five'
                    );
                    $_data[$arrs[$data['rate']]] = $model->{$arrs[$data['rate']]} + 1;

                    $count = 0;
                    for ($i = 1; $i < 6; $i++) {
                        if ($data['rate'] == $i)
                            $count += ($model->{$arrs[$i]} + 1) * $i;
                        else
                            $count += $model->{$arrs[$i]} * $i;
                    }
                    $_data['rate'] = round($count / $_data['rate_total'], 1);
                }

                if ($table_name == 'site') {
                    model('setting')->set_group('site-rating', $_data);
                } else
                    model($table_name)->update($model->id, $_data);
            }
            $data['created'] = now();
            $id =0;
            model("comment")->create($data,$id);

            //==gui thong bao
            if ($model) {
                // gui cho chu topic
                if ($model->user_id && $model->user_id != $user->id){
                    $url=$model->_url_view.'#goto=#reply_'.$id;
                    mod('user_notice')->send($model->user_id, $user->name . ' đã bình luận trong bài viết <b>'.$model->name.'</b>', ['url' => $url]);

                }
            }
            // Khai bao du lieu tra ve
            $result['complete'] = TRUE;

            if ($comment_active_status == config('status_on', 'main')) {
                $result['location'] = '';
                set_message(lang('notice_comment_success'));
            } else {
                $result['location'] = '';
                set_message(lang('notice_send_comment_success'));
            }
        } else {
            foreach ($params as $param) {
                $result[$param] = form_error($param);
            }
        }


        //Form Submit
        $this->_form_submit_output($result);
    }

    function reply($comment_id)
    {
        $user = user_get_account_info();
        if (!$user) {
            $result["modal_box"] = "modal-login-require";
            $this->_response($result);
        }
        // if(!mod("product")->setting('comment_allow'))
        // redirect();
        // Tai cac file thanh phan

        // Tu dong kiem tra gia tri cua 1 bien
        $param = $this->input->post('_autocheck');
        if ($param) {
            $this->_autocheck($param);
        }
        $comment = model('comment')->get_info($comment_id);
        if (!$comment) {
            return;
        }



        // Gan dieu kien cho cac bien
        $params = array('user', 'content');
        $this->_set_rules($params);

        // Xu ly du lieu
        $result = array();
        if ($this->form_validation->run()) {
            // Lay content
            $content = $this->input->post('content');
            $content = strip_tags($content);
            $table_id = $comment->table_id;
            $table_name = $comment->table_name;
            // Them du lieu vao data
            $data = array();
            $data['table_id'] = $table_id;
            $data['table_name'] = $table_name;
            $data['rate'] = floatval($this->input->post('rate'));
            $data['content'] = $content;
            $data['user_id'] = $user->id;
            $data['parent_id'] = $comment->id;
            $data['level'] = $comment->level + 1;
            $comment_active_status = 1;// mod("product")->setting('comment_auto_verify');

            if ($comment_active_status == config('status_on', 'main')) {
                $data['status'] = config('verify_yes', 'main');
            }
            $data['created'] = now();
            $data['reuped'] = $data['created'];
            //pr($data);
            model("comment")->create($data);

            // reup lai parent, va set la chua view
            model('comment')->update($comment->id, ["readed" => 0, "reuped" => now()]);

            //==them so lan nhan xet cho bang lien quan
            $model = $this->_update_table_infos($data, $table_name, $table_id);

            //==gui thong bao
            if ($model) {
                // gui cho chu topic
                if ($comment->user_id && $comment->user_id != $user->id){
                    mod('user_notice')->send($comment->user_id, $user->name . ' đã trả lởi bình luận của bạn', ['url' => $model->_url_view]);

                }
                // gui cho nhung nguoi dang binh luan topic nay
                $comments = model('comment')->filter_get_list(['parent_id' => $comment->id]);
                if ($comments) {
                    $users = array_gets($comments, 'user_id');
                    // khong gui thong bao cho nguoi gui binh luan
                    $users = array_diff($users, [$user->id]); // xoa nguoi binh luan khoi danh sach
                    if ($users) {
                        $msg = $user->name . ' đã bình luận chủ đề bạn quan tâm';
                        $url=$model->_url_view.'#goto=#reply_'.$comment->id;
                        foreach ($users as $v) {
                            mod('user_notice')->send($v, $msg, ['url' => $url]);
                        }
                    }

                }
            }
            //== Khai bao du lieu tra ve
            $result['complete'] = TRUE;

            if ($comment_active_status == config('status_on', 'main')) {
                $result['location'] = '';
                set_message(lang('notice_comment_success'));
            } else {
                $result['location'] = '';
                set_message(lang('notice_send_comment_success'));
            }
        } else {
            foreach ($params as $param) {
                $result[$param] = form_error($param);
            }
        }


        //Form Submit
        $this->_form_submit_output($result);
    }

    function vote($comment_id)
    {
        $user = user_get_account_info();
        if (!$user) {
            $result["modal_box"] = "modal-login-require";
            $this->_response($result);
        }

        $comment = model('comment')->get_info($comment_id);
        if (!$comment) {
            $this->_response();

        }


        $act = $this->input->get('act');
        if (!in_array($act, ['like', 'like_del', 'dislike', 'dislike_del']))
            $this->_response();



        if ( $comment->user_id == $user->id) {
            $this->_response(array('msg_toast' => lang('notice_dont_do_this_action')));
        }
        //kiem tra da luu hay chua
        $data = array();
        $data ['table_name'] = 'comment';
        $data ['table_id'] = $comment->id;
        $data ['user_id'] = $user->id;
        $point = null;
        if ($act == 'like') {
            $data ['like'] = 1;
            $data ['dislike'] = 0;
            $point = 1;
        } elseif ($act == 'like_del') {
            $data ['like'] = 0;
            $data ['dislike'] = 0;
            $point = -1;
        } elseif ($act == 'dislike') {
            $data ['like'] = 0;
            $data ['dislike'] = 1;
            $point = -1;
        } elseif ($act == 'dislike_del') {
            $data ['like'] = 0;
            $data ['dislike'] = 0;
            $point = 1;
        }

        $voted = model('social_vote')->get_info_rule(array('table_name' => 'comment', 'table_id' => $comment->id, 'user_id' => $user->id));
        if ($voted) {
            $data ['updated'] = now();
            model('social_vote')->update($voted->id, $data);
        } else {
            $data ['created'] = now();
            model('social_vote')->create($data);
        }

        $list = model('social_vote')->filter_get_list(array('table_name' => 'comment', 'table_id' => $comment->id));
        if ($list) {
            $d = 0;
            $p = 0;
            $stats = ['vote_total' => 0, 'vote_like' => 0, 'vote_dislike' => 0];
            foreach ($list as $row) {
                if ($row->like) {
                    $stats['vote_like']++;
                    $p++;
                } elseif ($row->dislike) {
                    $stats['vote_dislike']++;
                    $p--;
                }
                $d++;
            }
            $stats['vote_total'] = $d;
            $stats['point_total'] = $p;
        }


        //pr($stats);
        model('comment')->update($comment->id, $stats);
        model('user')->update_stats(['id' => $comment->user_id], ['point_total' => $point]);
        // pr_db();
        $result['element'] =  ['pos' => '#comment_' . $comment->id . '_vote_points', 'data' => $stats['point_total']];

        //$this->_response(array('msg_toast' => lang('notice_product_favorited')));
        $this->_response($result);
    }

    function show()
    {
        // Tai cac file thanh phan
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('file_model');

        $table_id = $this->uri->rsegment(4);
        $table_name = $this->uri->rsegment(3);
        if (!$this->_check_table_id($table_id, $table_name)) {
            return;
        }

        $filter = [
            'status' => config('verify_yes', 'main'),
            'table_id' => $table_id,
            'table_name' => $table_name
        ];
        $total = model('comment')->filter_get_total($filter);

        $page_size = $this->input->get('per_page');
        $page = $this->input->get('page');
        $limit = ($page - 1) * $page_size;
        $limit = max(0, $limit);

        $input = array();
        $input['order'] = array('created', 'DESC');
        $input['limit'] = array($limit, $page_size);

        $filter['parent_id'] = 0;

        // Lay danh sach
        $list = model('comment')->filter_get_list($filter, $input);
        foreach ($list as $row) {
            $user = model('user')->get_info($row->user_id, 'name');
            $row->user = $user;
            $image = $this->file_model->get_info_of_mod('user', $row->user_id, 'avatar', 'id, file_name');
            if ($image)
                $row->user->avatar = $image->file_name;


            if ($table_name == 'product')
                $row = mod('product')->comment_add_info($row);
            else
                $row = mod("product")->comment_add_info($row);
        }

        if ($table_name == 'product')
            $this->data['info'] = model('product')->get_info($table_id);
        else
            $this->data['info'] = model('lesson')->get_info($table_id);

        $this->data['list'] = $list;
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['page_size'] = $page_size;
        $this->data['ajax_pagination_total'] = ceil($total / $page_size);

        // Hien thi view
        $temp = 'site/_widget/' . $table_name . '/comment/_list';
        $this->load->view($temp, $this->data);
    }

    function _update_table_infos($data, $table_name, $table_id)
    {
        //them so lan nhan xet
        $model = model($table_name)->get_info($table_id);
        $model = mod($table_name)->add_info_url($model);
        $_data = array();
        $_data['comment_count'] = $model->comment_count + 1;
        if (isset($data['rate']) && $data['rate']) {
            $_data['rate_total'] = $model->rate_total + 1;
            $arrs = array(
                1 => 'rate_one',
                2 => 'rate_two',
                3 => 'rate_three',
                4 => 'rate_four',
                5 => 'rate_five'
            );
            $_data[$arrs[$data['rate']]] = $model->{$arrs[$data['rate']]} + 1;

            $count = 0;
            for ($i = 1; $i < 6; $i++) {
                if ($data['rate'] == $i)
                    $count += ($model->{$arrs[$i]} + 1) * $i;
                else
                    $count += $model->{$arrs[$i]} * $i;
            }
            $_data['rate'] = round($count / $_data['rate_total'], 1);
        }

        if ($table_name == 'site') {
            model('setting')->set_group('site-rating', $_data);
        } else
            model($table_name)->update($model->id, $_data);


        return $model;

    }

}
<div class="p20">

    <div class="row mb10">
        <div class="col-md-4">Tên thành viên</div>
        <div class="col-md-8"><b><?php echo $info->name ?></b></div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">Email</div>
        <div class="col-md-8">
            <?php echo $info->email ?>
        </div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">Chức danh/Lĩnh vực</div>
        <div class="col-md-8"><?php echo $info->profession ?></div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">Điện thoại</div>
        <div class="col-md-8"><?php echo $info->phone ?></div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">Facebook</div>
        <div class="col-md-8"><?php echo $info->facebook ?></div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">Website</div>
        <div class="col-md-8"><?php echo $info->website ?></div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">Địa chỉ</div>
        <div class="col-md-8"><?php echo $info->address ?></div>
    </div>

    <div class="row mb10">
        <div class="col-md-4">Ngày tham gia</div>
        <div class="col-md-8"><?php echo $info->_created ?></div>
    </div>
    <div class="row mb10">
        <div class="col-md-4">ID</div>
        <div class="col-md-8"><?php echo $info->_id ?></div>
    </div>
    <?php /* ?>
                <div class="row mb10">
                    <div class="col-md-4">Giới tính</div>
                    <div class="col-md-8">
                        <?php
                        switch($info->gender){
                            case '0':
                                echo "Không xác định";break;
                            case '1':
                                echo "Nam";break;
                            case '2':
                                echo "Nữ";break;
                            default:
                                echo 'Chưa khai báo';
                        }
                        ?>
                    </div>
                </div>
                <div class="row mb10">
                    <div class="col-md-4">Sinh năm</div>
                    <div class="col-md-8"><?php echo  $info->birthday?$info->birthday:'Chưa khai báo' ?></div>
                </div>
                 <div class="row">
                    <div class="col-md-4">Tình trạng hôn nhân</div>
                    <div class="col-md-8"><?php echo  $info->website ?></div>
                </div>
                <div class="row mb10">
                    <div class="col-md-4">Quốc tịch</div>
                    <div class="col-md-8">
                        <?php if(isset($info->_country_id))
                            echo  $info->_country_id_names ;
                        else echo  'Chưa khai báo'
                        ?>
                    </div>
                </div>
                <?php */ ?>
</div>

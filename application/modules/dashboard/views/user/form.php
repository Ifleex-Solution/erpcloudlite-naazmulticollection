<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel">

            <div class="panel-body">
                <?php
                echo form_open_multipart("dashboard/user/save_user/$user->user_id") ?>

                <?php echo form_hidden('id', $user->id) ?>

                <div class="form-group row">
                    <label for="firstname" class="col-sm-2 col-form-label text-d"><?php echo display('first_name') ?> <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        <input name="firstname" class="form-control" type="text" placeholder="<?php echo display('first_name') ?>" id="firstname" value="<?php echo $user->first_name ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="lastname" class="col-sm-2 col-form-label text-d"><?php echo display('last_name') ?> </label>
                    <div class="col-sm-8">
                        <input name="lastname" class="form-control" type="text" placeholder="<?php echo display('last_name') ?>" id="lastname" value="<?php echo $user->last_name ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label text-d">User Id<span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        <input name="email" class="form-control" type="text" placeholder="Username" id="email_id" value="<?php echo $user->email ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label text-d"><?php echo display('password') ?> <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        <input name="password" class="form-control" type="password" placeholder="<?php echo display('password') ?>" id="password" value="<?php echo $user->encrypted_password ?>" required>
                        <input name="oldpassword" class="form-control" type="hidden" value="<?php echo $user->encrypted_password ?>">
                    </div>
                </div>



                <div class="form-group row">
                    <label for="preview" class="col-sm-2 col-form-label"><?php echo display('preview') ?></label>
                    <div class="col-sm-2">
                        <img src="<?php echo base_url(!empty($user->image) ? $user->image : "./assets/img/icons/default.jpg") ?>" class="img-thumbnail" width="125" height="100">
                    </div>
                    <div class="col-sm-7">

                    </div>
                    <input type="hidden" name="old_image" id="old_image" value="<?php echo $user->image ?>">
                </div>
                <div class="form-group row">
                    <label for="image" class="col-sm-2 col-form-label"><?php echo display('image') ?></label>
                    <div class="col-sm-9">
                        <div>
                            <input type="file" name="image" id="edit_image" class="custom-input-file" />

                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="instance_type" class="col-sm-2 col-form-label">Home Page <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-8">
                        <select name="screen" id="screen" class="form-control" required tabindex="6" required>
                            <option value="">Select home page</option>
                            <option value="1"
                                <?php echo (isset($user->screen) && $user->screen == 1) ? 'selected' : ''; ?>>
                                Dashboard</option>

                            <option value="2"
                                <?php echo (isset($user->screen) && $user->screen == 2) ? 'selected' : ''; ?>>
                                Sale Invoice</option>

                            <option value="6"
                                <?php echo (isset($user->screen) && $user->screen == 6) ? 'selected' : ''; ?>>
                                Service Invoice</option>

                            <option value="7"
                                <?php echo (isset($user->screen) && $user->screen == 7) ? 'selected' : ''; ?>>
                                Purchase Invoice</option>

                            <option value="8"
                                <?php echo (isset($user->screen) && $user->screen == 8) ? 'selected' : ''; ?>>
                                GRN</option>

                            <option value="9"
                                <?php echo (isset($user->screen) && $user->screen == 9) ? 'selected' : ''; ?>>
                                GDN</option>

                            <option value="10"
                                <?php echo (isset($user->screen) && $user->screen == 10) ? 'selected' : ''; ?>>
                                Human Resource </option>
                            
                            <option value="11"
                                <?php echo (isset($user->screen) && $user->screen == 11) ? 'selected' : ''; ?>>
                                Company </option>

                            <option value="3"
                                <?php echo (isset($user->screen) && $user->screen == 3) ? 'selected' : ''; ?>>
                                Products</option>

                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="user_type" class="col-sm-2 col-form-label text-d">
                        Company<span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-8">
                        <select class="form-control" name="user_type" id="user_type" required>
                            <option value="">Select One</option>
                            <?php if (!empty($company_list)) { ?>
                                <?php foreach ($company_list as $data) { ?>
                                    <option value="<?= $data['company_id'] ?>" 
                                     <?php echo (isset($user->user_type) && $user->user_type == $data['company_id']) ? 'selected' : ''; ?>>
                                        <?= $data['company_name'] ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                
                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label text-d">User Period
                    <span class="text-danger">*</span></label>
                    <div class="col-sm-8">

                        <select class="form-control" id="temporary" name="temporary" tabindex="-1" aria-hidden="true" required>
                            <option value="">Select One</option>
                            <option value="1" <?php echo ($user->temporary == "1") ? 'selected' : ''; ?>>Enable</option>
                            <option value="0" <?php echo ($user->temporary == "0") ? 'selected' : ''; ?>>Disable</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label text-d">Start Date
                    <span class="text-danger">*</span></label>
                    <div class="col-sm-8">

                    <?php
                                date_default_timezone_set('Asia/Colombo');
                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="startdate" value="<?php echo  $user->startdate; ?>" id="startdate" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label text-d">End Date
                    <span class="text-danger">*</span></label>
                    <div class="col-sm-8">

                    <?php
                                date_default_timezone_set('Asia/Colombo');
                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="enddate" value="<?php echo  $user->enddate; ?>" id="enddate" />
                    </div>
                </div>


                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label text-d"><?php echo display('status') ?> <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true" required>
                            <option value="">Select One</option>
                            <option value="1" <?php echo ($user->status == "1") ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($user->status == "0") ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>


                <div class="form-group text-right">
                    <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo display('reset') ?></button>
                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('save') ?></button>
                </div>
                <?php echo form_close() ?>

            </div>
        </div>
    </div>
</div>
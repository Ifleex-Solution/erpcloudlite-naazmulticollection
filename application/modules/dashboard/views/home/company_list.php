<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span id="title"><?php echo display('company_list') ?></span>
                    <span class="padding-lefttitle">
                        <table>
                            <tr>
                                <td style="padding-left: 20px;">
                                    <?php if ($this->permission1->method('add_company', 'create')->access()) { ?>
                                        <a href="<?php echo base_url('add_company') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_company') ?> </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <style>
                        .company-name-nowrap {
                            white-space: nowrap;
                        }

                        .company-text-cell {
                            white-space: normal;
                            overflow: visible;
                            overflow-wrap: break-word;
                            word-break: break-word;
                        }

                        .company-fit-col {
                            width: 1%;
                            white-space: nowrap;
                        }

                        .company-equal-max-col {
                            width: 22%;
                            max-width: 22%;
                            min-width: 220px;
                        }

                        .company-status-badge {
                            display: inline-block;
                            min-width: 74px;
                        }

                        .company-action-col {
                            width: 95px;
                            min-width: 95px;
                            white-space: nowrap;
                        }

                        .company-action-wrap {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 4px;
                        }

                        .company-action-wrap .btn {
                            width: 30px;
                            height: 30px;
                            padding: 0;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                        }

                        .instance-dev {
                            background-color: #d9534f;
                            color: #fff;
                        }

                        .instance-uat {
                            background-color: #f0ad4e;
                            color: #fff;
                        }

                        .instance-beta {
                            background-color: #337ab7;
                            color: #fff;
                        }

                        .instance-prod {
                            background-color: #5cb85c;
                            color: #fff;
                        }

                        .instance-live {
                            background-color: #5cb85c;
                            color: #fff;
                        }

                        .instance-demo {
                            background-color: #6c757d;
                            color: #fff;
                        }

                        .instance-marketing {
                            background-color: #6f42c1;
                            color: #fff;
                        }
                    </style>

                    <table id="dataTableExample" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="company-fit-col"><?php echo display('sl') ?></th>
                                <th class="text-center company-fit-col"><?php echo display('name') ?></th>
                                <th class="text-center company-equal-max-col">Header Text</th>
                                <th class="text-center company-equal-max-col"><?php echo display('footer_text') ?></th>
                                <th class="text-center"><?php echo display('address') ?></th>
                                <th class="text-center company-fit-col">Instance</th>
                                <th class="text-center company-fit-col"><?php echo display('status') ?></th>
                                <th class="text-center company-action-col"><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($companys) {
                                $sl = 1;
                                foreach ($companys as $company) {
                                    // Define badge colors for each instance type
                                    $badge_class = 'default';
                                    switch ($company->instance_type) {
                                        case 'DEV':
                                            $badge_class = 'info';
                                            break;
                                        case 'UAT':
                                            $badge_class = 'warning';
                                            break;
                                        case 'PROD':
                                            $badge_class = 'success';
                                            break;
                                        case 'BETA':
                                            $badge_class = 'primary';
                                            break;
                                        case 'LIVE':
                                            $badge_class = 'danger';
                                            break;
                                        case 'DEMO':
                                            $badge_class = 'secondary';
                                            break;
                                        case 'MARKETING':
                                            $badge_class = 'dark';
                                            break;
                                    }
                            ?>

                                    <tr>
                                        <td class="company-fit-col"><?php echo $sl; ?></td>
                                        <td class="company-name-nowrap company-fit-col"><?php echo $company->company_name ?></td>
                                        <td class="company-text-cell company-equal-max-col"><?php echo !empty($company->header_text) ? $company->header_text : 'N/A'; ?></td>
                                        <td class="company-text-cell company-equal-max-col"><?php echo !empty($company->footer_text) ? $company->footer_text : 'N/A'; ?></td>
                                        <td class="company-text-cell"><?php echo $company->address ?></td>
                                        <td class="text-center company-fit-col">
                                            <span class="badge instance-<?php echo strtolower($company->instance_type ?: 'na'); ?>">
                                                <?php echo $company->instance_type ?: 'N/A'; ?>
                                            </span>
                                        </td>
                                        <td class="text-center company-fit-col">
                                            <?php if ($company->status == 1) { ?>
                                                <span class="label label-success company-status-badge"><?php echo display('active') ?></span>
                                            <?php } else { ?>
                                                <span class="label label-danger company-status-badge"><?php echo display('inactive') ?></span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center company-action-col">
                                            <div class="company-action-wrap">
                                                <?php echo form_open() ?>
                                                <?php if ($this->permission1->method('manage_company', 'update')->access()) { ?>
                                                    <a href="<?php echo base_url() . 'edit_company/' . $company->company_id; ?>"
                                                        class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left"
                                                        title="" data-original-title="<?php echo display('update') ?>"><i
                                                            class="fa fa-pencil" aria-hidden="true"></i></a>
                                                <?php } ?>
                                                <?php if (
                                                    $this->permission1->method('manage_company', 'delete')->access() &&
                                                    $company->company_id != 1 
                                                ) { ?>

                                                    <a href="<?php echo base_url('dashboard/setting/delete_company/' . $company->company_id); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        data-toggle="tooltip"
                                                        data-placement="left"
                                                        title="<?php echo display('delete'); ?>"
                                                        onclick="return confirm('Are you sure you want to delete this company?');">

                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>

                                                <?php } ?>
                                                <?php echo form_close() ?>
                                            </div>
                                        </td>
                                    </tr>

                            <?php
                                    $sl++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
    <div class="col-sm-12">
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px;">
            <div style="padding:16px 22px; border-bottom:1px solid #f1f5f9;">
                <span style="font-size:15px; font-weight:700; color:#1e293b;">
                    <i class="fa fa-info-circle" style="color:#3b82f6; margin-right:7px;"></i>
                    Company Configuration Instructions
                </span>
            </div>
            <div style="padding:18px 28px 20px 28px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    <li style="color:#1e293b; margin-bottom:12px; display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <span style="flex-shrink:0; margin-top:1px;">⚠️</span>
                        <span>A maximum of <strong>two (2) companies</strong> user can create within the system: one designated as the <strong>Primary Company</strong> for live business operations and one designated as the <strong>Secondary Company</strong> for testing, validation, or internal user training purposes.</span>
                    </li>
                    <li style="color:#1e293b; margin-bottom:12px; display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <span style="flex-shrink:0; margin-top:1px;">⚠️</span>
                        <span>The <strong>Primary Company</strong> serves as the official live operational environment and <strong>cannot be deleted, replaced, or reassigned</strong>. All official business transactions and operational records must be maintained within the Primary Company.</span>
                    </li>
                    <li style="color:#1e293b; margin-bottom:12px; display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <span style="flex-shrink:0; margin-top:1px;">⚠️</span>
                        <span>The <strong>Secondary Company</strong> is an optional environment created solely at the client's discretion and own choice. It is intended exclusively for testing invoice formats, printer layouts, report designs, workflow validation, and on-the-job training. It <strong>must not</strong> be used as a separate live operational environment.</span>
                    </li>
                    <li style="color:#1e293b; margin-bottom:12px; display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <span style="flex-shrink:0; margin-top:1px;">⚠️</span>
                        <span>The Primary Company and Secondary Company <strong>share the same inventory database</strong> to reduce database storage usage and optimize data management. Therefore, inventory-related transactions performed in the Secondary Company during testing or training <strong>may affect the inventory balances of the Primary Company</strong>. Users are advised to carefully review all transactions before proceeding.</span>
                    </li>
                    <li style="color:#1e293b; margin-bottom:12px; display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <span style="flex-shrink:0; margin-top:1px;">⚠️</span>
                        <span>Users are <strong>solely responsible</strong> for ensuring that testing or training activities performed in the Secondary Company do not interfere with or negatively impact live business operations.</span>
                    </li>
                    <li style="color:#1e293b; margin-bottom:0; display:flex; align-items:flex-start; gap:10px; font-size:14px;">
                        <span style="flex-shrink:0; margin-top:1px;">⚠️</span>
                        <span>Users are strictly advised to ensure that <strong>testing materials, reports, printouts, and system-generated documents remain confidential</strong> and are not shared, distributed, or disclosed outside the organization without proper authorization.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
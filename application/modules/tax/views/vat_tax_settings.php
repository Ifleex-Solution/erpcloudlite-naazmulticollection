<link href="<?php echo base_url('assets/css/vat_tax_settings.css') ?>" rel="stylesheet" type="text/css" />
<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('vat_tax_setting') ?> </h4>
                </div>
            </div>
            <div class="panel-body">
                <!-- Current Status Display -->
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Current Status:</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">
                            <strong id="current_status_text" style="font-size: 16px;">
                                <span id="status_badge" class="label"></span>
                            </strong>
                        </p>
                    </div>
                </div>

                <!-- VAT Tax Setting Dropdown -->
                <div class="form-group row">
                    <label for="vat_status" class="col-sm-4 col-form-label">VAT Tax Setting <i class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <select name="vat_status" id="vat_status" class="form-control" required">
                            <option value="">-- Select Status --</option>
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                        <small class="form-text text-muted">
                            Choose whether to enable or disable VAT tax calculations in the system.
                        </small>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="form-group row">
                    <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-6">
                        <input id="add-settings" class="btn btn-success" name="add-settings"
                            value="<?php echo display('save') ?>" type="button" onclick="save()" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo "<script>";
echo "let vtinfo=" . json_encode($vtinfo) . ";";
echo "</script>";
?>
<script>
    // Set the dropdown value and display current status
    document.addEventListener('DOMContentLoaded', function() {
        var vatStatus = vtinfo.ischecked == 1 ? '1' : '0';
        updateCurrentStatus(vatStatus);
    });

    // Update current status display
    function updateCurrentStatus(status) {
        var statusBadge = document.getElementById('status_badge');
        if (status == '1') {
            statusBadge.textContent = 'ENABLED';
            statusBadge.className = 'label label-success';
            statusBadge.style.fontSize = '14px';
            statusBadge.style.padding = '5px 10px';
        } else {
            statusBadge.textContent = 'DISABLED';
            statusBadge.className = 'label label-danger';
            statusBadge.style.fontSize = '14px';
            statusBadge.style.padding = '5px 10px';
        }
    }


    function save(){
        var ischecked = document.getElementById('vat_status').value;
        
        if (ischecked === '') {
            alert('Please select a VAT status (Enable or Disable)');
            return;
        }
        
        var confirmMsg = ischecked == '1' 
            ? 'Are you sure you want to ENABLE VAT Tax?' 
            : 'Are you sure you want to DISABLE VAT Tax?';
        
        if (!confirm(confirmMsg)) {
            return;
        }
        
        $.ajax({
            url: $('#baseUrl2').val() + 'tax/tax/save_vat',
            type: 'POST',
            data: {
                ischecked: ischecked
            },
            success: function(response) {
                var successMsg = ischecked == '1' 
                    ? 'VAT Tax has been ENABLED successfully!' 
                    : 'VAT Tax has been DISABLED successfully!';
                alert(successMsg);
                window.location.href = $('#baseUrl2').val() + 'vat_tax_setting';
            },
            error: function(error) {
                console.log(error);
                alert("Error updating VAT Status. Please try again.");
            }
        });
    }
</script>
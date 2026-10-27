<!-- ── Employee: Upload History ─────────────────────────────── -->
<div class="row" id="history_employee" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Employee</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkEmployeeTable">
                    <thead><tr>
                        <th><?php echo display('sl') ?></th>
                        <th>Upload ID</th><th>Date</th><th>Uploaded By</th>
                        <th><?php echo display('action') ?></th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function initEmployeeDT() {
    employeeDT = $('#bulkEmployeeTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkEmployeeUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}

/* rate_type: Hourly=1, Salary=2 */
var EMP_RATE_TYPE = { 'hourly': 1, 'salary': 2 };

function validateEmployeeRows(rows) {
    $('#preview_table_employee').show();

    /* build designation name → id map from all active+inactive designations */
    var desigMap = {};
    (csv_designations || []).forEach(function(d) {
        desigMap[d.designation.toLowerCase().trim()] = d.id;
    });

    var seenInCsv = new Set();

    for (var i = 0; i < rows.length; i++) {
        var r         = rows[i];
        var empId     = (r['Employee ID']          || '').trim();
        var empName   = (r['Employee Name']         || '').trim();
        var desigName = (r['Designation']           || '').trim();
        var payType   = (r['Pay Type']              || '').trim();
        var rateRaw   = (r['Rate']                  || '').trim();
        var blood     = (r['Blood Group']           || '').trim();
        var phone     = (r['Contact No.']           || '').trim();
        var email     = (r['Email']                 || '').trim();
        var addr1     = (r['Primary Address']       || '').trim();
        var addr2     = (r['Secondary Address']     || '').trim();
        var state     = (r['State/Province']        || '').trim();
        var country   = (r['Country']               || '').trim();
        var city      = (r['City']                  || '').trim();
        var zip       = (r['Zip code']              || '').trim();
        var statusRaw = (r['Status (Yes/No)']       || '').trim().toLowerCase();
        var error     = null;

        if (!empId)
            error = 'Employee ID is required';
        else if (seenInCsv.has(empId.toLowerCase()))
            error = 'Duplicate Employee ID in CSV: "' + empId + '"';
        else if (db_employee_ids.has(empId.toLowerCase()))
            error = 'Employee ID already exists: "' + empId + '"';

        if (!error && !empName)
            error = 'Employee Name is required';

        var desigId = null;
        if (!error) {
            if (!desigName) {
                error = 'Designation is required';
            } else {
                desigId = desigMap[desigName.toLowerCase()];
                if (desigId === undefined) error = 'Designation not found: "' + desigName + '"';
            }
        }

        if (!error && !['yes','no'].includes(statusRaw))
            error = 'Status must be Yes or No';

        var rateType = 0;
        if (!error && payType) {
            rateType = EMP_RATE_TYPE[payType.toLowerCase()];
            if (rateType === undefined) { error = 'Pay Type must be "Salary" or "Hourly"'; rateType = 0; }
        }

        if (!error && rateRaw !== '' && isNaN(parseFloat(rateRaw)))
            error = 'Rate must be a number';

        if (!error) seenInCsv.add(empId.toLowerCase());

        var status      = statusRaw === 'yes' ? 1 : 0;
        var statusLabel = status
            ? '<span class="label label-success">Active</span>'
            : '<span class="label label-default">Inactive</span>';
        var badge = error
            ? '<span class="label label-danger">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            previewTableData.push({ idx:i+1, empId:esc(empId), empName:esc(empName), desig:esc(desigName), payType:esc(payType||'—'), statusLabel:statusLabel, badge:badge, rowColor:'#fff5f5', hasError:true, _key:empId||String(i) });
        } else {
            validatedData.push({ _key: empId, payload: {
                last_name:      empId,
                first_name:     empName,
                designation:    desigId,
                rate_type:      rateType,
                hrate:          rateRaw !== '' ? parseFloat(rateRaw) : 0,
                blood_group:    blood,
                phone:          phone,
                email:          email,
                address_line_1: addr1,
                address_line_2: addr2,
                state:          state,
                country:        country,
                city:           city,
                zip:            zip,
                status:         status
            }});
            previewTableData.push({ idx:i+1, empId:esc(empId), empName:esc(empName), desig:esc(desigName), payType:esc(payType||'—'), statusLabel:statusLabel, badge:badge, rowColor:'', hasError:false, _key:empId });
        }
    }

    buildPreviewDT(
        ['#', 'Employee ID', 'Employee Name', 'Designation', 'Pay Type', 'Status', 'Validation'],
        ['idx', 'empId', 'empName', 'desig', 'payType', 'statusLabel', 'badge'],
        '#preview_table_employee'
    );
    finishValidation(rows.length);
}

function showBulkEmployeeDetails(id) {
    showGenericBulkDetails(
        id,
        'get_bulk_employee_details/',
        ['#', 'Employee ID', 'Employee Name', 'Designation', 'Pay Type', 'Rate', 'Phone', 'Email', 'Status'],
        ['emp_id', 'emp_name', 'designation', 'pay_type', 'hrate', 'phone', 'email', 'status_label'],
        'Employee Upload Details'
    );
}

function deleteBulkEmployee(id) {
    if (!confirm('Delete this upload record? The employee entries will remain in the system.')) return;
    $.post($('#base_url').val()+'delete_bulk_employee/'+id, function(){ if(employeeDT) employeeDT.ajax.reload(); });
}
</script>

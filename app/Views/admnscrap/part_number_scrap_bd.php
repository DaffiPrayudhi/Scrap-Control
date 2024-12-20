<?= $this->extend('layout/admnscrap'); ?>

<?= $this->section('title'); ?>
Part Number Scrap SMT
<?= $this->endSection(); ?>

<?= $this->section('content_header'); ?>
<h1>Part Number Scrap SMT</h1>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="content">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Input Data Part SMT</h3>
                            <div class="dropdown ml-2">
                                    <button class="btn btn-secondary btn-smsa dropdown-toggle float-right" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="<?= site_url('admnscrap/part_number_scrap_bd') ?>">Part Scrap SMT</a>
                                        <a class="dropdown-item" href="<?= site_url('admnscrap/part_number_scrap_db') ?>">Part Scrap FA</a>
                                    </div>
                            </div>
                        </div>
        
                        <form id="scrapForm" action="<?= base_url('user/submitScrapControl_bd'); ?>" method="post">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= session()->getFlashdata('success'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= session()->getFlashdata('error'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="card-body card-rs">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                            $session = session();
                                            $formData = $session->get('form_data');
                                        ?>
                                        <div class="form-group">
                                            <label for="tgl_bln_thn">Date</label>
                                            <input type="date" name="tgl_bln_thn" id="tgl_bln_thn" class="form-control" autocomplete="off" required 
                                            value="<?= old('tgl_bln_thn', isset($formData['tgl_bln_thn']) ? $formData['tgl_bln_thn'] : '') ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="shift">Shift</label>
                                            <input type="number" name="shift" id="shift" class="form-control" autocomplete="off" required 
                                            value="<?= old('shift', isset($formData['shift']) ? $formData['shift'] : '') ?>" maxlength="1" 
                                            pattern="\d{1,3}" oninput="this.value = this.value.replace(/[^1-2]/g, '').slice(0, 1);" placeholder="Input Shift Kerja">
                                        </div>

                                        <div class="form-group">
                                            <label for="line">Line</label>
                                            <select name="line" id="line" class="form-control" required>
                                                <option value="" disabled <?= old('line') ? '' : 'selected' ?>>Select Line</option>
                                                <?php foreach ($lines as $line): ?>
                                                    <option value="<?= $line['line']; ?>" <?= old('line', isset($formData['line']) ? $formData['line'] : '') == $line['line'] ? 'selected' : '' ?>><?= $line['line']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="model">Model</label>
                                            <select name="model" id="model" class="form-control" required>
                                                <option value="" disabled <?= old('model') ? '' : 'selected' ?>>Select Model</option>
                                                <?php if (old('model', isset($formData['model']) ? $formData['model'] : '')): ?>
                                                    <option value="<?= old('model', $formData['model']); ?>" selected><?= old('model', $formData['model']); ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="part_number">Part Number</label>
                                            <select name="part_number" id="part_number" class="form-control" required>
                                                <option value="" disabled <?= old('part_number') ? '' : 'selected' ?>>Select Part Number</option>
                                                <?php if (old('part_number', isset($formData['part_number']) ? $formData['part_number'] : '')): ?>
                                                    <option value="<?= old('part_number', $formData['part_number']); ?>" selected><?= old('part_number', $formData['part_number']); ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="scraptype">Tipe Scrap</label>
                                            <select name="scraptype" id="scraptype" class="form-control" required>
                                                <option value="" disabled selected>Select Tipe Scrap</option>
                                                <?php foreach ($scraptypes as $scraptype): ?>
                                                    <option value="<?= $scraptype['scraptype']; ?>"><?= $scraptype['scraptype']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="kategori">Mesin</label>
                                            <select name="kategori" id="kategori" class="form-control" required>
                                                <option value="" disabled selected>Select Mesin</option>
                                                <?php foreach ($kategoris1 as $kategori): ?>
                                                    <option value="<?= $kategori['kategori']; ?>"><?= $kategori['kategori']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="tipe_ng">Tipe NG</label>
                                            <select name="tipe_ng" id="tipe_ng" class="form-control" required>
                                                <option value="" disabled selected>Select Tipe NG</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Keterangan" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label for="qty">Qty Ng</label>
                                            <input type="number" name="qty" id="qty" class="form-control" autocomplete="off" required placeholder="Input Qty Ng">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-default" onclick="resetFields()">Reset</button>
                                    <button type="submit" class="btn btn-rs float-right" style="margin-right: 5px">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function focusOnSearchKey() {
        document.getElementById('').focus();
    }

    window.onload = focusOnSearchKey;

    function resetFields() {
    document.getElementById('tgl_bln_thn').value = '';
    document.getElementById('shift').value = '';
    document.getElementById('line').selectedIndex = 0; 
    document.getElementById('model').innerHTML = '<option value="" disabled selected>Select Model</option>';
    document.getElementById('part_number').innerHTML = '<option value="" disabled selected>Select Part Number</option>';
    document.getElementById('scraptype').selectedIndex = 0; 
    document.getElementById('kategori').selectedIndex = 0; 
    document.getElementById('tipe_ng').innerHTML = '<option value="" disabled selected>Select Tipe NG</option>';
    document.getElementById('remarks').value = '';  
    document.getElementById('qty').value = '';
    }

    document.querySelector('.btn-default').addEventListener('click', function() {
        resetFields();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var storedLine = '<?= session()->get('form_data.line') ?? ''; ?>';
        var storedModel = '<?= session()->get('form_data.model') ?? ''; ?>';
        var storedPartNumber = '<?= session()->get('form_data.part_number') ?? ''; ?>';

        if (storedLine) {
            populateModels(storedLine, storedModel);
        }

        if (storedModel && storedLine) {
            populatePartNumbers(storedModel, storedLine, storedPartNumber);
        }
    });

    document.getElementById('line').addEventListener('change', function() {
        var line = this.value;
        var modelSelect = document.getElementById('model');
        modelSelect.innerHTML = '<option value="" disabled selected>Select Model</option>';

        fetch('<?= base_url('user/getModelsByLine'); ?>/' + encodeURIComponent(line))
            .then(response => response.json())
            .then(data => {
                data.models.forEach(model => {
                    var option = document.createElement('option');
                    option.value = model.model;
                    option.text = model.model;
                    modelSelect.add(option);
                });
            })
            .catch(error => console.error('Error:', error));
    });

    document.getElementById('model').addEventListener('change', function() {
        var model = this.value;
        var line = document.getElementById('line').value;
        var partNumberSelect = document.getElementById('part_number');
        partNumberSelect.innerHTML = '<option value="" disabled selected>Select Part Number</option>';

        fetch('<?= base_url('user/getPartNumbersByModelAndLine'); ?>/' + encodeURIComponent(model) + '/' + encodeURIComponent(line))
            .then(response => response.json())
            .then(data => {
                data.part_numbers.forEach(part => {
                    var option = document.createElement('option');
                    option.value = part.part_number;
                    option.text = part.part_number;
                    partNumberSelect.add(option);
                });
            })
            .catch(error => console.error('Error:', error));
    });

    function populateModels(line, selectedModel) {
        var modelSelect = document.getElementById('model');
        modelSelect.innerHTML = '<option value="" disabled>Select Model</option>';

        fetch('<?= base_url('user/getModelsByLine'); ?>/' + encodeURIComponent(line))
            .then(response => response.json())
            .then(data => {
                data.models.forEach(model => {
                    var option = document.createElement('option');
                    option.value = model.model;
                    option.text = model.model;
                    if (model.model === selectedModel) {
                        option.selected = true;
                    }
                    modelSelect.add(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }

    function populatePartNumbers(model, line, selectedPartNumber) {
        var partNumberSelect = document.getElementById('part_number');
        partNumberSelect.innerHTML = '<option value="" disabled>Select Part Number</option>';

        fetch('<?= base_url('user/getPartNumbersByModelAndLine'); ?>/' + encodeURIComponent(model) + '/' + encodeURIComponent(line))
            .then(response => response.json())
            .then(data => {
                data.part_numbers.forEach(part => {
                    var option = document.createElement('option');
                    option.value = part.part_number;
                    option.text = part.part_number;
                    if (part.part_number === selectedPartNumber) {
                        option.selected = true;
                    }
                    partNumberSelect.add(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<script>
    document.getElementById('kategori').addEventListener('change', function() {
        var kategori = this.value;
        var tipeNgSelect = document.getElementById('tipe_ng');

        tipeNgSelect.innerHTML = '<option value="" disabled selected>Select Tipe NG</option>';

        if (kategori) {
            fetch('<?= base_url('user/getKategori'); ?>/' + kategori)
                .then(response => response.json())
                .then(data => {
                    data.forEach(part => {
                        var option = document.createElement('option');
                        option.value = part.tipe_ng;
                        option.text = part.tipe_ng;
                        tipeNgSelect.add(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>

<style>

    .dropdown-toggle::after {
        display: none; 
    }

    .dropdown-menu {
        min-width: 200px; 
    }

    .dropdown-item {
        padding: 10px 15px; 
        color: #000; 
    }

    .dropdown-item:hover {
        background-color: #e9ecef; 
    }

    .btn-smsa {
        background-color: #0069aa;
        color: #fff;
        border-color: #0069aa;
    }

    .btn-smsa:focus,
    .btn-smsa:hover,
    .btn-smsa:active {
        background-color: #005d96;
        border-color: #0069aa;
        color: #fff; 
        box-shadow: none;
    }

    #search_results {
        margin-top: 10px;
        max-height: 150px;
        overflow-y: auto; 
    }

    .search-results-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .search-results-list li {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
    }

    .search-results-list li:hover {
        background-color: #f1f1f1;
    }

    #search_results p {
        color: #666;
        font-style: italic;
    }

    #notif-cond {
        max-height: 450px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .notification-box {
        background-color: darkorange;
        color: white;
        width: 100%;
        box-sizing: border-box;
        padding: 20px;
        margin: 10px 0;
        font-size: 20px;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .no-notification {
        display: none;
    }

    .table-responsive {
        font-size: 12.2px;
        overflow-y: hidden;
        overflow-x: hidden;
        max-height: 390px;
    }

    .table-responsive:hover {
        overflow-y: auto;
    }

    .table-fixed-header tbody {
    background-color: whitesmoke;
    }
    
    .table-fixed-header thead th {
        position: sticky;
        top: 0;
        background-color: #f5911f;
        color: #000;
        text-align: center;
        z-index: 999;
    }

    .card-header {
        background-color: #0069aa;
        color: #fff;
    }

    .card-rs {
        padding: 20px 20px 0 20px;
        font-size: 14px;
    }

    .card-sr {
        padding: 0 20px 20px 20px;
    }

    .content {
        padding-top: 8px;
    }

    .btn-rs { 
        background-color: #0069aa;
        color: #fff;
    }

    .btn-rs:hover {
        background-color: #014f80;
        color: #fff;
    }

    .card-description {
        font-size: 12px;
        color: #555;
        padding: 0px; 
    }

    .red-color {
        color: #DC4C64;
        font-weight: bold;
    }

    .red-color-hg {
        color: #fff;
        background-color: #DC4C64;
        font-weight: bold;
        padding: 5px;
    }

    .black-color {
        color: #fff;
        background-color: #000;
        font-weight: bold;
        padding: 5px;
    }

    @media (min-width: 768px) and (max-width: 1024px) {
    .table {
        font-size: 10px; 
    }

    .table-fixed-header {
        max-height: 400px; /* tinggi tabel pada tablet */
    }

    .card-body, .card-body-rs {
        max-height: 400px; /* tinggi card body */
    }

    .card-body-rs {
        padding: 10px;
    }

    .table th, .table td {
        padding: 8px; 
    }

    .table th, .table td {
        word-wrap: break-word;
    }

    .search-container {
        margin: 10px 0;
    }

    .search-box {
        width: 100%;
        font-size: 14px;
    }

    .center-card-title {
        font-size: 14px; 
    }

    .btn-rs { 
        background-color: #0069aa;
        color: #fff;
        padding: 5px;
    }
}
</style>
<?= $this->endSection(); ?>




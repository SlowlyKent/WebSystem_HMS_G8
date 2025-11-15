<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>

<div class="dashboard-welcome mb-4">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <i class="fas fa-edit fa-2x me-3"></i>
      <div>
        <h2 class="mb-1">Edit Patient</h2>
        <p class="mb-0 opacity-75">Update patient information below.</p>
      </div>
    </div>
    <div>
      <a href="<?= site_url('admin/patients/registration') ?>" class="btn btn-outline-secondary me-2">
        <i class="fas fa-arrow-left me-2"></i>Back to List
      </a>
      <a href="<?= site_url('admin/patients/view/' . $patient['id']) ?>" class="btn btn-outline-primary">
        <i class="fas fa-eye me-2"></i>View Patient
      </a>
    </div>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success mb-3"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Edit Patient Record - <?= esc($patient['patient_code']) ?></h5>
  </div>
  <div class="card-body">
    <form action="<?= site_url('admin/patients/update/' . $patient['id']) ?>" method="post" class="row g-3">
      <?= csrf_field() ?>
      
      <div class="col-md-4">
        <label class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" value="<?= old('first_name', $patient['first_name'] ?? '') ?>" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" value="<?= old('middle_name', $patient['middle_name'] ?? '') ?>" class="form-control">
      </div>

      <div class="col-md-4">
        <label class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" value="<?= old('last_name', $patient['last_name'] ?? '') ?>" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= old('date_of_birth', $patient['date_of_birth'] ?? '') ?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
          <option value="">Select gender</option>
          <option value="Male" <?= (old('gender', $patient['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= (old('gender', $patient['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
          <option value="Other" <?= (old('gender', $patient['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="<?= old('phone', $patient['phone'] ?? '') ?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= old('email', $patient['email'] ?? '') ?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">Select status</option>
          <option value="Inpatient" <?= (old('status', $patient['status'] ?? '') === 'Inpatient') ? 'selected' : '' ?>>Inpatient</option>
          <option value="Outpatient" <?= (old('status', $patient['status'] ?? '') === 'Outpatient') ? 'selected' : '' ?>>Outpatient</option>
          <option value="Discharged" <?= (old('status', $patient['status'] ?? '') === 'Discharged') ? 'selected' : '' ?>>Discharged</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Blood Type</label>
        <select name="blood_type" class="form-select" id="bloodTypeSelect">
          <option value="">Select blood type</option>
          <?php foreach ($bloodTypes as $type): ?>
            <?php $selected = (old('blood_type', $patient['blood_type'] ?? '') === $type) ? 'selected' : ''; ?>
            <option value="<?= esc($type) ?>" <?= $selected ?>><?= esc($type) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Province (Region 12)</label>
        <select name="province" id="provinceSelect" class="form-select">
          <option value="">Select Province</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">City / Municipality</label>
        <select name="city_municipality" id="citySelect" class="form-select" disabled>
          <option value="">Select City / Municipality</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Barangay</label>
        <select name="barangay" id="barangaySelect" class="form-select" disabled>
          <option value="">Select Barangay</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Street</label>
        <input type="text" name="street" value="<?= old('street', $address['street'] ?? '') ?>" class="form-control" placeholder="Street / Purok / Lot">
      </div>

      <div class="col-12">
        <div class="p-4 border rounded-3 bg-light-subtle">
          <div class="d-flex align-items-center mb-3">
            <div class="me-3">
              <span class="badge rounded-circle bg-primary-subtle text-primary p-3">
                <i class="fas fa-shield-heart"></i>
              </span>
            </div>
            <div>
              <h5 class="mb-0">Insurance Information</h5>
              <small class="text-muted">Selecting a provider will auto-fill the coverage percentage.</small>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Insurance Provider</label>
              <select name="insurance_provider" class="form-select" id="insuranceProviderSelect">
                <option value="">Select insurance provider</option>
                <?php foreach ($insuranceProviders as $value => $pct): ?>
                  <?php $selected = (old('insurance_provider', $patient['insurance_provider'] ?? '') === $value) ? 'selected' : ''; ?>
                  <option value="<?= esc($value) ?>" data-coverage="<?= esc($pct) ?>" <?= $selected ?>>
                    <?= esc($value) ?> (<?= esc($pct) ?>%)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Policy Number</label>
              <input type="text" name="insurance_policy_no" value="<?= old('insurance_policy_no', $patient['insurance_policy_no'] ?? '') ?>" class="form-control" placeholder="Enter policy number">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Coverage Percentage (%)</label>
              <select name="insurance_coverage_pct" class="form-select" id="coveragePercentSelect">
                <option value="">Select Coverage %</option>
                <?php foreach ($coverageOptions as $pct): ?>
                  <?php $selected = ((string) old('insurance_coverage_pct', $patient['insurance_coverage_pct'] ?? '') === (string) $pct) ? 'selected' : ''; ?>
                  <option value="<?= esc($pct) ?>" <?= $selected ?>><?= esc($pct) ?>%</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Maximum Coverage per Bill (₱)</label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="text" name="insurance_max_per_bill" value="<?= old('insurance_max_per_bill', $patient['insurance_max_per_bill'] ?? '') ?>" class="form-control" placeholder="e.g., 50000.00" inputmode="decimal">
              </div>
              <small class="text-muted">Maximum amount insurance will pay per bill (optional)</small>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Valid Until</label>
              <input type="date" name="insurance_valid_until" value="<?= old('insurance_valid_until', $patient['insurance_valid_until'] ?? '') ?>" class="form-control" placeholder="mm/dd/yyyy">
              <small class="text-muted">Insurance expiration date</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <label class="form-label">Initial Medical Notes</label>
        <textarea name="medical_notes" class="form-control" rows="3" placeholder="Any initial observations or notes..."><?= old('medical_notes', $patient['medical_notes'] ?? '') ?></textarea>
      </div>

      <div class="col-12 d-flex justify-content-end">
        <a href="<?= site_url('admin/patients/registration') ?>" class="btn btn-secondary me-2">
          <i class="fas fa-times me-2"></i>Cancel
        </a>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save me-2"></i>Update Patient
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // -----------------------
  // Insurance auto-coverage
  // -----------------------
  const providerSelect = document.getElementById('insuranceProviderSelect');
  const coverageSelect = document.getElementById('coveragePercentSelect');
  const coverageMap = <?= json_encode($insuranceProviders) ?>;

  if (providerSelect && coverageSelect) {
    const applyAutoCoverage = () => {
      const selectedProvider = providerSelect.value;
      if (selectedProvider && coverageMap[selectedProvider]) {
        coverageSelect.value = String(coverageMap[selectedProvider]);
      }
    };

    providerSelect.addEventListener('change', applyAutoCoverage);

    // Prefill on load if user selected a provider but no coverage yet
    if (!coverageSelect.value) {
      applyAutoCoverage();
    }
  }

  // -----------------------
  // Region 12 address data
  // -----------------------
  const region12Data = {
    'South Cotabato': {
      'Koronadal City': ['Zone III', 'Zone IV', 'San Isidro'],
      'Polomolok': ['Cannery Site', 'Poblacion', 'Glamang'],
      'Tupi': ['Poblacion', 'Linan', 'Polonuling']
    },
    'Sarangani': {
      'Alabel': ['Poblacion', 'Paraiso', 'Kawas'],
      'Glan': ['Poblacion', 'Gumasa', 'Tango'],
      'Maasim': ['Poblacion', 'Amsipit', 'Seven Hills'],
      'Maitum': ['Poblacion', 'Kalaong', 'Upo'],
      'Malapatan': ['Poblacion', 'Lun Masla', 'Lun Padidu', 'Sapu Padidu'],
      'Malungon': ['Poblacion', 'Domolok', 'Nagpan']
    },
    'Cotabato (North)': {
      'Kidapawan City': ['Poblacion', 'Lano', 'Sudapin'],
      'Midsayap': ['Poblacion 1', 'Bual Norte', 'Kayaga'],
      'Kabacan': ['Poblacion', 'Buluan', 'Simoney']
    },
    'Sultan Kudarat': {
      'Isulan': ['Kalawag I', 'Kalawag II', 'Kalawag III'],
      'Tacurong City': ['Poblacion', 'Buenaflor', 'New Carmen'],
      'Lebak': ['Poblacion', 'Kulaman', 'Purus']
    }
  };

  const provinceSelectEl = document.getElementById('provinceSelect');
  const citySelectEl = document.getElementById('citySelect');
  const barangaySelectEl = document.getElementById('barangaySelect');

  function resetSelect(selectEl, placeholderText) {
    if (!selectEl) return;
    selectEl.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = placeholderText;
    selectEl.appendChild(opt);
    selectEl.value = '';
    selectEl.disabled = true;
  }

  if (provinceSelectEl && citySelectEl && barangaySelectEl) {
    const oldProvince = '<?= esc(old('province', $address['province'] ?? ''), 'js') ?>';
    const oldCity = '<?= esc(old('city_municipality', $address['city_municipality'] ?? ''), 'js') ?>';
    const oldBarangay = '<?= esc(old('barangay', $address['barangay'] ?? ''), 'js') ?>';

    resetSelect(provinceSelectEl, 'Select Province');
    Object.keys(region12Data).forEach(function (provinceName) {
      const opt = document.createElement('option');
      opt.value = provinceName;
      opt.textContent = provinceName;
      if (oldProvince === provinceName) {
        opt.selected = true;
      }
      provinceSelectEl.appendChild(opt);
    });
    provinceSelectEl.disabled = false;

    provinceSelectEl.addEventListener('change', function () {
      const selectedProvince = this.value;
      resetSelect(citySelectEl, 'Select City / Municipality');
      resetSelect(barangaySelectEl, 'Select Barangay');

      if (!selectedProvince || !region12Data[selectedProvince]) {
        return;
      }

      const cities = Object.keys(region12Data[selectedProvince]);
      cities.forEach(function (cityName) {
        const opt = document.createElement('option');
        opt.value = cityName;
        opt.textContent = cityName;
        if (oldCity === cityName) {
          opt.selected = true;
        }
        citySelectEl.appendChild(opt);
      });
      citySelectEl.disabled = false;

      // Trigger city change if old city exists
      if (oldCity && region12Data[selectedProvince][oldCity]) {
        citySelectEl.dispatchEvent(new Event('change'));
      }
    });

    citySelectEl.addEventListener('change', function () {
      const selectedProvince = provinceSelectEl.value;
      const selectedCity = this.value;
      resetSelect(barangaySelectEl, 'Select Barangay');

      if (!selectedProvince || !selectedCity) {
        return;
      }

      const barangays = region12Data[selectedProvince][selectedCity] || [];
      barangays.forEach(function (brgyName) {
        const opt = document.createElement('option');
        opt.value = brgyName;
        opt.textContent = brgyName;
        if (oldBarangay === brgyName) {
          opt.selected = true;
        }
        barangaySelectEl.appendChild(opt);
      });
      barangaySelectEl.disabled = barangays.length === 0;
    });

    // Initialize with existing values
    if (oldProvince && region12Data[oldProvince]) {
      provinceSelectEl.dispatchEvent(new Event('change'));
    }
  }
});
</script>

<?= $this->endSection() ?>


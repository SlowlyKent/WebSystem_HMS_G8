<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <i class="fas fa-flask fa-2x me-3"></i>
      <div>
        <h2 class="mb-1">Laboratory & Diagnostics</h2>
        <p class="mb-0 opacity-75">Manage lab test requests, samples, and results</p>
      </div>
    </div>
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width: 600px;">
        <strong>Error:</strong> <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width: 600px;">
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
          <?php 
          $errors = session()->getFlashdata('errors');
          if (is_array($errors)) {
            foreach ($errors as $field => $msg): ?>
              <li><?= esc($msg) ?></li>
            <?php endforeach;
          } else {
            echo '<li>' . esc($errors) . '</li>';
          }
          ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRequestModal">
      <i class="fas fa-plus me-2"></i>New Lab Request
    </button>
  </div>
</div>

<!-- Lab Requests Table -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-0 py-3">
    <h5 class="mb-0">Lab Requests</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Request Code</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Request Date</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $req): ?>
              <tr>
                <td><strong><?= esc($req['request_code'] ?? 'N/A') ?></strong></td>
                <td>
                  <?= esc($req['patient_first'] ?? '') ?> <?= esc($req['patient_last'] ?? '') ?>
                  <br><small class="text-muted"><?= esc($req['patient_code'] ?? '') ?></small>
                </td>
                <td>
                  Dr. <?= esc($req['doctor_first'] ?? '') ?> <?= esc($req['doctor_last'] ?? '') ?>
                </td>
                <td><?= esc($req['request_date'] ?? '') ?></td>
                <td>
                  <?php
                    $priority = $req['priority'] ?? 'routine';
                    $badgeClass = $priority === 'urgent' ? 'bg-danger' : ($priority === 'stat' ? 'bg-warning' : 'bg-info');
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= ucfirst($priority) ?></span>
                </td>
                <td>
                  <?php
                    $status = $req['status'] ?? 'pending';
                    $statusClass = $status === 'completed' ? 'bg-success' : 
                                   ($status === 'in_progress' ? 'bg-warning' : 
                                   ($status === 'sample_collected' ? 'bg-info' : 'bg-secondary'));
                  ?>
                  <span class="badge <?= $statusClass ?>"><?= ucfirst(str_replace('_', ' ', $status)) ?></span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="<?= base_url('laboratory/results/' . $req['id']) ?>" class="btn btn-outline-primary" title="View Results">
                      <i class="fas fa-eye"></i>
                    </a>
                    <?php if ($status === 'pending'): ?>
                      <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#collectSampleModal<?= $req['id'] ?>" title="Collect Sample">
                        <i class="fas fa-vial"></i>
                      </button>
                    <?php endif; ?>
                    <?php if ($status === 'sample_collected' || $status === 'in_progress'): ?>
                      <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#enterResultsModal<?= $req['id'] ?>" title="Enter Results">
                        <i class="fas fa-edit"></i>
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>

              <!-- Collect Sample Modal -->
              <div class="modal fade" id="collectSampleModal<?= $req['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="<?= base_url('laboratory/sample/' . $req['id']) ?>" method="post">
                      <?= csrf_field() ?>
                      <div class="modal-header">
                        <h5 class="modal-title">Collect Sample</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Sample Type</label>
                          <select name="sample_type" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Blood">Blood</option>
                            <option value="Urine">Urine</option>
                            <option value="Stool">Stool</option>
                            <option value="Tissue">Tissue</option>
                            <option value="Sputum">Sputum</option>
                            <option value="Other">Other</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Collection Date & Time</label>
                          <input type="datetime-local" name="collection_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Notes (optional)</label>
                          <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Record Collection</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Enter Results Modal -->
              <div class="modal fade" id="enterResultsModal<?= $req['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <form action="<?= base_url('laboratory/results/' . $req['id']) ?>" method="post">
                      <?= csrf_field() ?>
                      <div class="modal-header">
                        <h5 class="modal-title">Enter Test Results</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <p class="text-muted">Enter results for each test type:</p>
                        <?php
                          // Get test types for this request
                          $db = \Config\Database::connect();
                          $testTypes = $db->table('lab_request_items lri')
                            ->select('ltt.*')
                            ->join('lab_test_types ltt', 'ltt.id = lri.test_type_id')
                            ->where('lri.request_id', $req['id'])
                            ->get()
                            ->getResultArray();
                        ?>
                        <?php foreach ($testTypes as $test): ?>
                          <div class="card mb-3">
                            <div class="card-body">
                              <h6><?= esc($test['name']) ?></h6>
                              <p class="text-muted small mb-2">
                                Normal Range: <?= esc($test['normal_range'] ?? 'N/A') ?> 
                                <?= esc($test['unit'] ?? '') ?>
                              </p>
                              <div class="row">
                                <div class="col-md-6">
                                  <label class="form-label">Result Value</label>
                                  <input type="text" name="result_<?= $test['id'] ?>" class="form-control" placeholder="Enter result">
                                </div>
                                <div class="col-md-3">
                                  <label class="form-label">Status</label>
                                  <select name="is_normal_<?= $test['id'] ?>" class="form-select">
                                    <option value="1">Normal</option>
                                    <option value="0">Abnormal</option>
                                  </select>
                                </div>
                                <div class="col-md-3">
                                  <label class="form-label">Notes</label>
                                  <input type="text" name="notes_<?= $test['id'] ?>" class="form-control" placeholder="Optional">
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Results</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No lab requests found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Create Request Modal -->
<div class="modal fade" id="createRequestModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?= base_url('laboratory/request') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Create Lab Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Patient <span class="text-danger">*</span></label>
              <select name="patient_id" class="form-select" required>
                <option value="">-- Select Patient --</option>
                <?php foreach ($patients as $patient): ?>
                  <option value="<?= $patient['id'] ?>">
                    <?= esc($patient['last_name'] . ', ' . $patient['first_name']) ?> 
                    (<?= esc($patient['patient_code'] ?? '') ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Doctor <span class="text-danger">*</span></label>
              <?php
                $db = \Config\Database::connect();
                $doctors = $db->table('doctors d')
                  ->select('d.id, u.first_name, u.last_name')
                  ->join('users u', 'u.id = d.user_id')
                  ->where('u.status', 'active')
                  ->get()
                  ->getResultArray();
              ?>
              <select name="doctor_id" class="form-select" required>
                <option value="">-- Select Doctor --</option>
                <?php foreach ($doctors as $doctor): ?>
                  <option value="<?= $doctor['id'] ?>">
                    Dr. <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Request Date <span class="text-danger">*</span></label>
              <input type="date" name="request_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select">
                <option value="routine">Routine</option>
                <option value="urgent">Urgent</option>
                <option value="stat">STAT</option>
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Test Types <span class="text-danger">*</span></label>
              <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                <?php foreach ($testTypes as $test): ?>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="test_types[]" value="<?= $test['id'] ?>" id="test_<?= $test['id'] ?>">
                    <label class="form-check-label" for="test_<?= $test['id'] ?>">
                      <strong><?= esc($test['name']) ?></strong> 
                      <span class="text-muted">(<?= esc($test['category'] ?? 'N/A') ?>)</span>
                      <span class="badge bg-secondary ms-2">₱<?= number_format((float)$test['price'], 2) ?></span>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Notes (optional)</label>
              <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>


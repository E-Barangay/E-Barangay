<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$complaintID = $_GET['complaintID'] ?? $_POST['complaintID'] ?? '';
$successMessage = '';
$errorMessage = '';

if (!empty($complaintID)) {
    $checkQuery = "SELECT complaintID FROM complaints WHERE complaintID = " . (int)$complaintID;
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
        header("Location: ../index.php?page=complaints");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveComplaint'])) {
  $complaintID = (int) $_POST['complaintID'];
  $isVawcCase = isset($_POST['isVawcCase']) ? 1 : 0;

  if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../uploads/evidence/';
    if (!file_exists($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['evidence']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetPath)) {
      $filePath = 'uploads/evidence/' . $fileName;
      $insertEvidence = "INSERT INTO complaintevidence (complaintID, filePath, uploadedAt) 
                        VALUES ($complaintID, '$filePath', NOW())";
      mysqli_query($conn, $insertEvidence);
    }
  }

  $updateFields = [];
  $complaintType = mysqli_real_escape_string($conn, $_POST['complaintType'] ?? '');
  $complaintTitle = mysqli_real_escape_string($conn, $_POST['complaintTitle'] ?? '');
  $complainantName = mysqli_real_escape_string($conn, $_POST['complainantName'] ?? '');
  $complaintPhoneNumber = mysqli_real_escape_string($conn, $_POST['complaintPhoneNumber'] ?? '');
  $complaintDescription = mysqli_real_escape_string($conn, $_POST['complaintDescription'] ?? '');
  $actionTaken = mysqli_real_escape_string($conn, $_POST['actionTaken'] ?? '');
  $complaintVictim = mysqli_real_escape_string($conn, $_POST['complaintVictim'] ?? '');
  $victimAge = (int) ($_POST['victimAge'] ?? 0);
  $complaintAccused = mysqli_real_escape_string($conn, $_POST['complaintAccused'] ?? '');
  $victimRelationship = mysqli_real_escape_string($conn, $_POST['victimRelationship'] ?? '');
  $complaintAddress = mysqli_real_escape_string($conn, $_POST['complaintAddress'] ?? '');
  $complaintLatitude = mysqli_real_escape_string($conn, $_POST['complaintLatitude'] ?? '');
  $complaintLongitude = mysqli_real_escape_string($conn, $_POST['complaintLongitude'] ?? '');

  $updateFields[] = "complaintType = '$complaintType'";
  $updateFields[] = "complaintTitle = '$complaintTitle'";
  $updateFields[] = "complainantName = '$complainantName'";
  $updateFields[] = "complaintPhoneNumber = '$complaintPhoneNumber'";
  $updateFields[] = "complaintDescription = '$complaintDescription'";
  $updateFields[] = "actionTaken = '$actionTaken'";
  $updateFields[] = "complaintVictim = '$complaintVictim'";
  $updateFields[] = "victimAge = $victimAge";
  $updateFields[] = "complaintAccused = '$complaintAccused'";
  $updateFields[] = "victimRelationship = '$victimRelationship'";
  $updateFields[] = "complaintAddress = '$complaintAddress'";
  $updateFields[] = "complaintLatitude = '$complaintLatitude'";
  $updateFields[] = "complaintLongitude = '$complaintLongitude'";

  if ($isVawcCase) {
    $updateFields[] = "complaintStatus = 'VAWC'";
  } else if (isset($_POST['complaintStatus'])) {
    $complaintStatus = mysqli_real_escape_string($conn, $_POST['complaintStatus']);
    $updateFields[] = "complaintStatus = '$complaintStatus'";
  }

  $updateQuery = "UPDATE complaints SET " . implode(', ', $updateFields) . " WHERE complaintID = $complaintID";

  if (mysqli_query($conn, $updateQuery)) {
    $successMessage = "Complaint updated successfully!";
    header("Location: " . $_SERVER['PHP_SELF'] . "?complaintID=" . $complaintID);
    exit;
  } else {
    $errorMessage = "Error updating complaint: " . mysqli_error($conn);
  }
}

$detailQuery = "
  SELECT 
    r.complaintID, 
    r.requestDate, 
    r.complaintStatus, 
    r.complaintType,
    r.complaintTitle,
    COALESCE(CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName), r.complainantName) AS complainantName,
    r.complaintVictim,
    r.complaintDescription,
    r.actionTaken,
    r.victimAge,
    r.complaintAccused,
    r.victimRelationship,
    r.complaintPhoneNumber,
    r.evidence,
    r.complaintAddress,
    r.complaintLatitude,
    r.complaintLongitude
  FROM complaints r
  LEFT JOIN users u ON r.userID = u.userID
  LEFT JOIN userinfo ui ON u.userID = ui.userID
  WHERE r.complaintID = $complaintID
";
$detailResult = mysqli_query($conn, $detailQuery);
$complaint = mysqli_fetch_assoc($detailResult);

$evidenceQuery = "SELECT * FROM complaintevidence WHERE complaintID = $complaintID ORDER BY uploadedAt DESC";
$evidenceResult = mysqli_query($conn, $evidenceQuery);
$evidenceFiles = [];
if ($evidenceResult) {
  while ($row = mysqli_fetch_assoc($evidenceResult)) {
    $evidenceFiles[] = $row;
  }
}

$isVawcCase = (strtoupper($complaint['complaintStatus']) === 'VAWC');
$badgeClass = 'bg-info';
if (in_array(strtolower($complaint['complaintStatus']), ['criminal', 'civil'])) {
  $badgeClass = 'bg-danger';
} elseif (in_array(strtolower($complaint['complaintStatus']), ['mediation', 'conciliation', 'arbitration'])) {
  $badgeClass = 'bg-info';
} elseif (in_array(strtolower($complaint['complaintStatus']), ['repudiated', 'withdrawn', 'pending', 'dismissed', 'certified'])) {
  $badgeClass = 'bg-success';
} elseif (strtolower($complaint['complaintStatus']) === 'vawc') {
  $badgeClass = 'bg-danger';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaint Details</title>
  <link rel="icon" href="../../assets/images/logoSanAntonio.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
</head>

<body class="bg-light" style="font-family: 'Poppins', sans-serif;">
  <div class="container-fluid p-3 p-md-4">
    <form method="POST" action="" enctype="multipart/form-data" id="complaintForm">
      <input type="hidden" name="complaintID" value="<?= $complaint['complaintID'] ?>">
      <input type="hidden" name="complaintLatitude" id="hiddenLatitude"
        value="<?= $complaint['complaintLatitude'] ?? '' ?>">
      <input type="hidden" name="complaintLongitude" id="hiddenLongitude"
        value="<?= $complaint['complaintLongitude'] ?? '' ?>">
      <input type="hidden" name="complaintAddress" id="hiddenAddress"
        value="<?= $complaint['complaintAddress'] ?? '' ?>">

      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
          style="background-color: #31afab;">
          <div class="d-flex align-items-center">
            <h4 class="mb-0 fw-semibold">Complaint Details</h4>
          </div>
          <a href="../index.php?page=complaints" class="btn btn-outline-light btn-sm">Back to List</a>
        </div>
      </div>

      <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <?= $successMessage ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <?= $errorMessage ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                  <span class="view-mode"><?= htmlspecialchars($complaint['complaintTitle']) ?></span>
                  <input type="text" class="form-control d-none edit-mode" name="complaintTitle"
                    value="<?= htmlspecialchars($complaint['complaintTitle']) ?>">
                </h5>
                <span class="badge <?= $badgeClass ?> text-white px-3 py-2 rounded-pill">
                  <?= ucfirst($complaint['complaintStatus']) ?>
                </span>
              </div>
            </div>

            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Date Submitted:</strong>
                    <span><?= $complaint['requestDate'] ?></span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Evidence:</strong>
                    <div class="view-mode">
                      <?php if (count($evidenceFiles) > 0): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                          data-bs-target="#evidenceModal">
                          View Evidence (<?= count($evidenceFiles) ?> file<?= count($evidenceFiles) > 1 ? 's' : '' ?>)
                        </button>
                      <?php else: ?>
                        <span class="text-muted">No evidence</span>
                      <?php endif; ?>
                    </div>
                    <div class="edit-mode d-none">
                      <input type="file" class="form-control" name="evidence" id="evidenceInput"
                        accept="image/*,application/pdf">
                      <small class="text-muted">Upload new evidence file</small>
                      <div id="evidencePreview" class="mt-2"></div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Complainant:</strong>
                    <span class="view-mode"><?= htmlspecialchars($complaint['complainantName']) ?></span>
                    <input type="text" class="form-control d-none edit-mode" name="complainantName"
                      value="<?= htmlspecialchars($complaint['complainantName']) ?>" readonly>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Phone Number:</strong>
                    <span class="view-mode"><?= htmlspecialchars($complaint['complaintPhoneNumber']) ?></span>
                    <input type="text" class="form-control d-none edit-mode" name="complaintPhoneNumber"
                      value="<?= htmlspecialchars($complaint['complaintPhoneNumber']) ?>">
                  </div>
                </div>

                <div class="col-12">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Description:</strong>
                    <span class="view-mode"><?= nl2br(htmlspecialchars($complaint['complaintDescription'])) ?></span>
                    <textarea class="form-control d-none edit-mode" name="complaintDescription"
                      rows="3"><?= htmlspecialchars($complaint['complaintDescription']) ?></textarea>
                  </div>
                </div>

                <div class="col-12">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Action:</strong>
                    <span class="view-mode"><?= nl2br(htmlspecialchars($complaint['actionTaken'])) ?></span>
                    <textarea class="form-control d-none edit-mode" name="actionTaken"
                      rows="3"><?= htmlspecialchars($complaint['actionTaken']) ?></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Victim:</strong>
                    <span class="view-mode"><?= htmlspecialchars($complaint['complaintVictim']) ?></span>
                    <input type="text" class="form-control d-none edit-mode" name="complaintVictim"
                      value="<?= htmlspecialchars($complaint['complaintVictim']) ?>">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Victim Age:</strong>
                    <span class="view-mode"><?= htmlspecialchars($complaint['victimAge']) ?></span>
                    <input type="number" class="form-control d-none edit-mode" name="victimAge"
                      value="<?= htmlspecialchars($complaint['victimAge']) ?>">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Perpetrator:</strong>
                    <span class="view-mode"><?= htmlspecialchars($complaint['complaintAccused']) ?></span>
                    <input type="text" class="form-control d-none edit-mode" name="complaintAccused"
                      value="<?= htmlspecialchars($complaint['complaintAccused']) ?>">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="border-bottom pb-3">
                    <strong class="text-muted d-block mb-2">Relationship:</strong>
                    <span class="view-mode"><?= htmlspecialchars($complaint['victimRelationship']) ?></span>
                    <input type="text" class="form-control d-none edit-mode" name="victimRelationship"
                      value="<?= htmlspecialchars($complaint['victimRelationship']) ?>">
                  </div>
                </div>

                <div class="col-12 mt-4">
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <h6 class="fw-semibold mb-3">Location Map</h6>

                      <div class="view-mode">
                        <div class="mb-3">
                          <label class="form-label"><strong>Address:</strong></label>
                          <div class="p-2 bg-white rounded border">
                            <?= htmlspecialchars($complaint['complaintAddress'] ?? 'No address specified') ?>
                          </div>
                        </div>
                      </div>

                      <div class="edit-mode d-none">
                        <div class="mb-3">
                          <label class="form-label"><strong>Address:</strong></label>
                          <div class="input-group mb-2">
                            <input type="text" id="addressInput" class="form-control" placeholder="Enter address"
                              value="<?= htmlspecialchars($complaint['complaintAddress'] ?? '') ?>">
                            <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                          </div>
                          <small class="text-muted">You can drag the marker to pin exact location</small>
                        </div>
                      </div>

                      <div id="map" class="rounded shadow-sm mb-3" style="height:300px;"></div>

                      <div id="coordInfo"
                        class="alert alert-info <?= empty($complaint['complaintLatitude']) ? 'd-none' : '' ?>">
                        <strong>Pinned Location:</strong> Lat: <span
                          id="latDisplay"><?= $complaint['complaintLatitude'] ?></span>, Lng: <span
                          id="lngDisplay"><?= $complaint['complaintLongitude'] ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom">
              <h5 class="mb-0">Status & Actions</h5>
            </div>
            <div class="card-body">
              <div class="mb-4 text-center">
                <span class="badge <?= $badgeClass ?> text-white px-3 py-2 fs-6">
                  <?= $complaint['complaintStatus'] ?>
                </span>
              </div>

              <div class="mb-4">
                <div class="form-check edit-mode d-none">
                  <input class="form-check-input" type="checkbox" name="isVawcCase" id="vawcCheck" <?= $isVawcCase ? 'checked' : '' ?>>
                  <label class="form-check-label fw-semibold" for="vawcCheck">
                    Mark as VAWC Case
                  </label>
                </div>
                <?php if ($isVawcCase): ?>
                  <div class="alert alert-danger view-mode">
                    <strong>⚠️ VAWC CASE</strong>
                  </div>
                <?php endif; ?>
              </div>

              <div id="statusControls">
                <div class="view-mode">
                  <?php if (!$isVawcCase): ?>
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Dispute Type:</label>
                      <div class="p-2 bg-light rounded">
                        <?= htmlspecialchars($complaint['complaintType'] ?? '')?>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Status:</label>
                      <div class="p-2 bg-light rounded">
                        <?= htmlspecialchars($complaint['complaintStatus']) ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="edit-mode d-none" id="editStatusControls">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Change Dispute:</label>
                    <select name="complaintType" class="form-select">
                      <?php foreach (['Criminal', 'Civil', 'Others'] as $type): ?>
                        <option value="<?= $type ?>" <?= $complaint['complaintType'] == $type ? 'selected' : '' ?>>
                          <?= $type ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Change Status:</label>
                    <select name="complaintStatus" class="form-select" id="statusSelect">
                      <?php foreach (['Mediation', 'Conciliation', 'Arbitration', 'Repudiated', 'Withdrawn', 'Pending', 'Dismissed', 'Certified'] as $status): ?>
                        <option value="<?= $status ?>" <?= $complaint['complaintStatus'] == $status ? 'selected' : '' ?>>
                          <?= $status ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button type="button" id="editBtn" class="btn btn-warning view-mode">Edit</button>
                <div class="edit-mode d-none">
                  <button type="submit" name="saveComplaint" class="btn btn-success w-100 mb-2">Save Changes</button>
                  <button type="button" id="cancelBtn" class="btn btn-danger w-100">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  <?php if (count($evidenceFiles) > 0): ?>
    <div class="modal fade" id="evidenceModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Evidence Files</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <?php foreach ($evidenceFiles as $evidence): ?>
                <div class="col-md-6">
                  <div class="card">
                    <img src="../../<?= htmlspecialchars($evidence['filePath']) ?>" class="card-img-top"
                      style="max-height: 300px; object-fit: contain;">
                    <div class="card-body">
                      <small class="text-muted">Uploaded: <?= $evidence['uploadedAt'] ?></small>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let map, marker;
    const defaultLat = 14.115;
    const defaultLng = 121.155;
    let currentLat = <?= !empty($complaint['complaintLatitude']) ? $complaint['complaintLatitude'] : 'null' ?>;
    let currentLng = <?= !empty($complaint['complaintLongitude']) ? $complaint['complaintLongitude'] : 'null' ?>;
    let isEditMode = false;

    function initMap() {
      const initialLat = currentLat || defaultLat;
      const initialLng = currentLng || defaultLng;

      map = L.map('map', {
        dragging: false,
        touchZoom: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        zoomControl: true
      }).setView([initialLat, initialLng], currentLat ? 16 : 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      if (currentLat && currentLng) {
        marker = L.marker([currentLat, currentLng], { draggable: false }).addTo(map);
      }
    }

    function enableMapInteraction() {
      map.dragging.enable();
      map.touchZoom.enable();
      map.scrollWheelZoom.enable();
      map.doubleClickZoom.enable();
      map.boxZoom.enable();
      map.keyboard.enable();

      if (marker) {
        marker.dragging.enable();
        marker.on('dragend', updateCoordinates);
      }

      map.on('click', function (e) {
        if (marker) {
          map.removeLayer(marker);
        }
        marker = L.marker([e.latlng.lat, e.latlng.lng], { draggable: true }).addTo(map);
        marker.on('dragend', updateCoordinates);
        updateCoordinates(e);
      });
    }

    function disableMapInteraction() {
      map.dragging.disable();
      map.touchZoom.disable();
      map.scrollWheelZoom.disable();
      map.doubleClickZoom.disable();
      map.boxZoom.disable();
      map.keyboard.disable();

      map.off('click');

      if (marker) {
        marker.dragging.disable();
        marker.off('dragend');
      }
    }

    function updateCoordinates(e) {
      const pos = e.latlng || e.target.getLatLng();
      currentLat = pos.lat;
      currentLng = pos.lng;

      document.getElementById('latDisplay').textContent = currentLat.toFixed(6);
      document.getElementById('lngDisplay').textContent = currentLng.toFixed(6);
      document.getElementById('hiddenLatitude').value = currentLat;
      document.getElementById('hiddenLongitude').value = currentLng;
      document.getElementById('coordInfo').classList.remove('d-none');
    }

    document.getElementById('searchBtn')?.addEventListener('click', function () {
      const address = document.getElementById('addressInput').value.trim();
      if (!address) {
        alert('Please enter an address');
        return;
      }

      document.getElementById('hiddenAddress').value = address;

      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
        .then(response => response.json())
        .then(data => {
          if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lng = parseFloat(data[0].lon);

            map.setView([lat, lng], 16);

            if (marker) {
              map.removeLayer(marker);
            }

            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', updateCoordinates);

            currentLat = lat;
            currentLng = lng;
            document.getElementById('latDisplay').textContent = lat.toFixed(6);
            document.getElementById('lngDisplay').textContent = lng.toFixed(6);
            document.getElementById('hiddenLatitude').value = lat;
            document.getElementById('hiddenLongitude').value = lng;
            document.getElementById('coordInfo').classList.remove('d-none');
          } else {
            alert('Location not found. You can still click on the map to pin a location.');
          }
        })
        .catch(error => {
          console.error('Search error:', error);
          alert('Error searching location. You can still click on the map to pin a location.');
        });
    });

    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const vawcCheck = document.getElementById('vawcCheck');

    function toggleEditMode(isEditing) {
      isEditMode = isEditing;

      document.querySelectorAll('.view-mode').forEach(el => {
        el.classList.toggle('d-none', isEditing);
      });
      document.querySelectorAll('.edit-mode').forEach(el => {
        el.classList.toggle('d-none', !isEditing);
      });

      // Handle map interaction
      if (isEditing) {
        enableMapInteraction();
      } else {
        disableMapInteraction();
      }

      // Handle status controls visibility based on VAWC checkbox
      if (isEditing && vawcCheck) {
        const isVawc = vawcCheck.checked;
        document.getElementById('editStatusControls').classList.toggle('d-none', isVawc);
      }
    }

    if (editBtn) editBtn.addEventListener('click', () => toggleEditMode(true));
    if (cancelBtn) cancelBtn.addEventListener('click', () => location.reload());

    if (vawcCheck) {
      vawcCheck.addEventListener('change', function () {
        document.getElementById('editStatusControls').classList.toggle('d-none', this.checked);
      });
    }

    document.getElementById('evidenceInput')?.addEventListener('change', function (e) {
      if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        const file = e.target.files[0];
        reader.onload = function (event) {
          const preview = document.getElementById('evidencePreview');
          preview.innerHTML = '';

          if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'img-thumbnail';
            img.style.maxWidth = '150px';
            preview.appendChild(img);
          } else {
            preview.innerHTML = '<small class="text-success">✓ File selected: ' + file.name + '</small>';
          }
        };
        reader.readAsDataURL(file);
      }
    });

    window.addEventListener('load', initMap);
  </script>
</body>

</html>
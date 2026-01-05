<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-info-circle me-2"></i><?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
</div>

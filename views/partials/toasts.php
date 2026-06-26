<?php
  $toastErrors = $errors ?? [];
  $toastSuccess = $successMessage ?? null;
?>

<?php if (!empty($toastErrors) || !empty($toastSuccess)): ?>
  <div class="toast-stack" aria-live="polite" aria-atomic="true">
    <?php if (!empty($toastSuccess)): ?>
      <div class="toast toast--success" role="status" data-toast>
        <p class="toast__message"><?= htmlspecialchars($toastSuccess) ?></p>
        <button class="toast__close" type="button" aria-label="Fermer la notification" data-toast-close>
          ×
        </button>
      </div>
    <?php endif; ?>

    <?php foreach ($toastErrors as $error): ?>
      <div class="toast toast--error" role="alert" data-toast>
        <p class="toast__message"><?= htmlspecialchars($error) ?></p>
        <button class="toast__close" type="button" aria-label="Fermer la notification" data-toast-close>
          ×
        </button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

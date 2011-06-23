<form action="<?php echo uri('/fedora-connector/datastreams/' . $id . '/import'); ?>" method="post" class="button-form fedora-inline-form">
  <input type="submit" value="Import" class="fedora-inline-button bagit-create-bag">
</form>

<form action="<?php echo uri('/fedora-connector/datastreams/' . $id . '/delete'); ?>" method="post" class="button-form fedora-inline-form">
  <input type="hidden" name="confirm" value="false" />
  <input type="submit" value="Delete" class="fedora-inline-button fedora-delete">
</form>

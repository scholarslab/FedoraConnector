<form action="<?php echo uri('/fedora-connector/servers/edit/' . $id); ?>" method="post" class="button-form fedora-inline-form">
  <input type="submit" value="Edit" class="fedora-inline-button bagit-create-bag">
</form>

<form action="<?php echo uri('/fedora-connector/servers/delete/' . $id); ?>" method="post" class="button-form fedora-inline-form">
  <input type="hidden" name="confirm" value="false" />
  <input type="submit" value="Delete" class="fedora-inline-button fedora-delete">
</form>

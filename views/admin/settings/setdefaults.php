<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Import Settings')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <p class="fedora-explanation">In this section, you can configure the plugin's behavior during the import process.
In many cases, when a datastream is imported into Omeka, Fedora Connector will find new data for a
Dublin Core field in the datastream that is different from the data that was there to start with. Often, this
just means that the plugin has found updated information that should replace the existing data. But, depending
on your workflow, in other situations there may be data that was entered into the DC fields in Omeka that should
<i>not</i> be overwritten by information in the datastream.</p>

    <p class="fedora-explanation">By default, Fedora will always add information from a datastream if the corresponding
Dublin Core field in Omeka is empty. If there is a discrepancy, Fedora Connector can do one of three things:</p>

    <ul class="fedora-explanation" style="list-style: disc;">
        <li><strong>Overwrite</strong>: Replace the existing data the new data from the datastream. The old data is permanently deleted.</li>
        <li><strong>Stack</strong>: Add the new data as an additional "element" on the field. This stacks the new data underneath the old data, thereby retaining everything.</li>
        <li><strong>Block</strong>: If there is a discrepancy, throw out the new data and keep the original value for the field.</li>
    </ul>

    <p class="fedora-explanation">Use the dropdown boxes below to set installation-wide default behaviors for each field.
These settings can be customized for individual items through the "Fedora Import Settings" tab in the item editing interface.</p>

<hr class="fedora-divider">

<form enctype="multipart/form-data" action="<?php echo uri('/fedora-connector/settings/save'); ?>" method="post">

<h2>Universal Default Setting:</h2>

    <div class="fedora-defaults-field">

        <select name="behavior_default">
            <option value="overwrite"<?php if ($defaultImportBehavior == 'overwrite') { echo ' SELECTED'; } ?>>Overwrite</option>
            <option value="stack"<?php if ($defaultImportBehavior == 'stack') { echo ' SELECTED'; } ?>>Stack</option>
            <option value="block"<?php if ($defaultImportBehavior == 'block') { echo ' SELECTED'; } ?>>Block</option>
        </select>

    </div>

<h2 style="margin-top: 2em;">Per-Field Default Settings:</h2>

    <?php foreach ($elements as $element): ?>

    <?php
        $import = $importBehavior->getDefaultBehavior($element, true);
    ?>

        <div class="fedora-defaults-field">

            <h3><strong><?php echo $element->name . ':'; if ($import != 'default') { echo ' <span style="color: #C50;">[Non-Default]</span>'; } ?></strong></h3>

            <select name="behavior[<?php echo $element->name; ?>]">
                <option value="default"<?php if ($import == 'default') { echo ' SELECTED'; } ?>>(default)</option>
                <option value="overwrite"<?php if ($import == 'overwrite') { echo ' SELECTED'; } ?>>Overwrite</option>
                <option value="stack"<?php if ($import == 'stack') { echo ' SELECTED'; } ?>>Stack</option>
                <option value="block"<?php if ($import == 'block') { echo ' SELECTED'; } ?>>Block</option>
            </select>

        </div>

    <?php endforeach; ?>

    <input type="submit" name="fedora-settings-submit" id="fedora-settings-submit" value="Save Settings" />

</form>

</div>



<?php foot(); ?>

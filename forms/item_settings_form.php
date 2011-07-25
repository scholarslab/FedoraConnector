<p class="fedora-explanation">Use the dropdown boxes below to set item-specific behaviors for each field.</p>

<p class="fedora-explanation">By default, Fedora will always add information from a datastream if the corresponding
Dublin Core field in Omeka is empty. If there is a discrepancy, Fedora Connector can do one of three things:</p>

<ul class="fedora-explanation" style="list-style: disc;">
    <li><strong>Overwrite</strong>: Replace the existing data the new data from the datastream. The old data is permanently deleted.</li>
    <li><strong>Stack</strong>: Add the new data as an additional "element" on the field. This stacks the new data underneath the old data, thereby retaining everything.</li>
    <li><strong>Block</strong>: If there is a discrepancy, throw out the new data and keep the original value for the field.</li>
</ul>


<hr class="fedora-divider">

<h2 style="margin-top: 2em;">Per-Field Settings:</h2>

    <?php foreach ($elements as $element): ?>

        <?php
            $import = $db->getTable('FedoraConnectorImportBehaviorItem')->getBehaviorForSelect($element->name);
            $addifempty = $db->getTable('FedoraConnectorAddToBlankItem')->getBehaviorForSelect($element->name);
        ?>

        <div class="fedora-defaults-field">

            <h3><strong><?php echo $element->name; if ($import != false || $addifempty != false) { echo ' <span style="color: #C50;">[Non-Default]</span>'; } ?></strong>:</h3>

            <span>Import Behavior:</span>
            <select name="behavior[<?php echo $element->name; ?>]">
                <option value="default"<?php if ($import == false) { echo ' SELECTED'; } ?>>(default)</option>
                <option value="overwrite"<?php if ($import == 'overwrite') { echo ' SELECTED'; } ?>>Overwrite</option>
                <option value="stack"<?php if ($import == 'stack') { echo ' SELECTED'; } ?>>Stack</option>
                <option value="block"<?php if ($import == 'block') { echo ' SELECTED'; } ?>>Block</option>
            </select>

            <span>Add new data if field is empty?</span>
            <select name="addifempty[<?php echo $element->name; ?>]">
                <option value="default"<?php if ($addifempty == false) { echo ' SELECTED'; } ?>>(default)</option>
                <option value="yes"<?php if ($addifempty == 'yes') { echo ' SELECTED'; } ?>>Yes</option>
                <option value="no"<?php if ($addifempty == 'no') { echo ' SELECTED'; } ?>>No</option>
            </select>

        </div>

    <?php endforeach; ?>

</div>

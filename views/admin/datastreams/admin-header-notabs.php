<?php

    $header = array('title' => html_escape('Fedora Connector | ' . $subtitle), 'content_class' => 'horizontal-nav');
    head($header);

?>

<h1><?php echo $header['title']; ?></h1>
<ul id="section-nav" class="navigation">
<?php echo nav(array(
    'Create Datastream' => uri('fedora-connector/datastreams/create'),
))?>
</ul>

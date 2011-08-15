<?php

    $header = array('title' => html_escape('Fedora Connector | ' . $subtitle), 'content_class' => 'horizontal-nav');
    head($header);

?>

<h1 class="fedora-nowrap"><?php echo $header['title']; ?></h1>
<ul id="section-nav" class="navigation">
<?php echo nav(array(
    'Datastreams'  => uri('fedora-connector/datastreams'),
    'Servers' => uri('fedora-connector/servers'),
    'Import Settings'  => uri('fedora-connector/settings')
))?>
</ul>

<?php

    $header = array('title' => html_escape('Fedora Connector | ' . $subtitle), 'content_class' => 'horizontal-nav');
    head($header);

?>

<h1 class="fedora-nowrap"><?php echo $header['title']; ?></h1>
<ul id="section-nav" class="navigation">
<?php echo nav(array(
    'Servers' => uri('fedora-connector/servers'),
    'Datastreams'  => uri('fedora-connector/datastreams'),
    'Import Settings'  => uri('fedora-connector/settings')
))?>
</ul>

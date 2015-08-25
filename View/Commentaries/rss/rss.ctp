<?php
$this->set('channelData', array(
    'title' => __("Weekly Commentary with Michael Hicks"),
    'link' => $this->Html->url('/', true),
    'description' => __("Weekly commentaries by Michael J. Hicks Ph.D., director of the Center for Business and Economic Research, Ball State University."),
    'language' => 'en-us'
));

App::uses('Sanitize', 'Utility');

foreach ($commentaries as $commentary) {
    $commentaryTime = strtotime($commentary['Commentary']['published_date']);

    $commentaryLink = array(
        'controller' => 'commentaries',
        'action' => 'view',
        'id' => $commentary['Commentary']['id'],
        'slug' => $commentary['Commentary']['slug']
    );

    // This is the part where we clean the body text for output as the description
    // of the rss item, this needs to have only text to make sure the feed validates
    $description = $commentary['Commentary']['body'];
    $description = preg_replace('=\(.*?\)=is', '', $description);
    $description = $this->Text->stripLinks($description);
    $description = Sanitize::stripAll($description);
    $description = $this->Text->truncate($description, 600, array(
        'ending' => '...',
        'exact'  => false,
        'html'   => true,
    ));
	$date = date('F j, Y', strtotime($commentary['Commentary']['published_date']));

    echo $this->Rss->item(array(), array(
        'title' => $date.': '.$commentary['Commentary']['title'],
        'link' => $commentaryLink,
        'guid' => array('url' => $commentaryLink, 'isPermaLink' => 'true'),
        'description' => $description,
        'pubDate' => $commentary['Commentary']['published_date']
    ));
}
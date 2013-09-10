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
    $combined_body = $commentary['Commentary']['body'];
    if (! empty($commentary['Commentary']['summary'])) {
    	$combined_body = '<p><strong>'.$commentary['Commentary']['summary'].'</strong></p>'.$combined_body;
    }
    $combined_body = preg_replace('=\(.*?\)=is', '', $combined_body);
    $combined_body = $this->Text->stripLinks($combined_body);
    $combined_body = Sanitize::stripAll($combined_body);
    $combined_body = $this->Text->truncate($combined_body, 600, array(
        'ending' => '...',
        'exact'  => false,
        'html'   => true,
    ));
	$date = date('F j, Y', strtotime($commentary['Commentary']['published_date']));
    
    echo $this->Rss->item(array(), array(
        'title' => $date.': '.$commentary['Commentary']['title'],
        'link' => $commentaryLink,
        'guid' => array('url' => $commentaryLink, 'isPermaLink' => 'true'),
        'description' => $combined_body,
        'pubDate' => $commentary['Commentary']['published_date']
    ));
}
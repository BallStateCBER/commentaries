function toggleDelayPublishing() {
	var current_time = new Date();
	var this_month = current_time.getMonth() + 1;
	if (this_month < 10) {
		this_month = '0' + this_month;
	}
	var this_day = current_time.getDate();
	if (this_day < 10) {
		this_day = '0' + this_day;
	}
	var this_year =  current_time.getFullYear();
	var selected_month = $('#CommentaryPublishedDateMonth').val();
	var selected_day = $('#CommentaryPublishedDateDay').val();
	var selected_year = $('#CommentaryPublishedDateYear').val();
	var selected_date = selected_year + selected_month + selected_day;
	var this_date = this_year + this_month + this_day;
	if (selected_date > this_date) {
		$('#delayed_publishing_date').html('automatically on ' + selected_month + '-' + selected_day + '-' + selected_year);
	} else {
		$('#delayed_publishing_date').html('');
	}
}

function selectTagSublist(letter) {
	$('#tag_sublist_loading ul.tag_sublist').each(function() {
		this.hide();
	});
	if (letter == 'cloud') {
		$('#tag_cloud').show();
	} else {
		$('#tag_cloud').hide();
		$('#tag_sublist_' + letter).show();
	}
}
<?php echo $name; ?>,

We have received a request (hopefully from you) for your password to be reset so you can log in to your account at <?php echo $site_url; ?>. When you visit the following webpage, you'll be prompted to enter a new password to overwrite your old one:

<?php echo $reset_url; ?> 

NOTE: That link will only work for the rest of <?php echo date('F Y'); ?>. If you need to reset your password in <?php echo date('F', strtotime('+1 month')); ?> or later, you'll need to request another password reset link. This precaution prevents anyone from finding this email at a later date and using it to gain unauthorized access to your account. Nonetheless, you are advised to delete this email after you have reset your password.

Ball State Center for Business and Economic Research
cber@bsu.edu
www.bsu.edu/cber
765-285-5926
<?php
/*
 * This file is part of GBook - PHP Guestbook.
 *
 * (c) Copyright 2016 by Klemen Stirn. All rights reserved.
 * http://www.phpjunkyard.com
 * http://www.phpjunkyard.com/php-guestbook-script.php
 *
 * For the full copyright and license agreement information, please view
 * the docs/index.html file that was distributed with this source code.
 */

/*
 * LANGUAGE: English (US)
 * Translated by: Klemen Stirn
 *
 * !!! This file must be saved in UTF-8 encoding without byte order mark (BOM) !!!
 * Test chars: àáâãäåæ
 */

// Encoding for HTML pages and emails
$lang['enc']='utf-8'; // DO NOT CHANGE, must be utf-8!
$lang['lng']='English';

// Error messages
$lang['e02']='Invalid ID';
$lang['e03']='Please enter your name';
$lang['e04']='Enter a valid e-mail address or leave it empty';
$lang['e05']='Enter a valid website address or leave it empty';
$lang['e06']='Please enter your comments';
$lang['e07']='Please enter the security number';
$lang['e08']='Wrong security number';
$lang['e09']='Please enter your password';
$lang['e10']='Please enter your reply message';
$lang['e11']='This is not a valid action!';
$lang['e12']='Wrong password!';
$lang['e13']='Can\'t open entries file for writing. Please CHMOD entries file to 666 (rw-rw-rw-)!';
$lang['e14']='The bad words file can\'t be found! Please check the name of the file. On most servers names are CaSe SeNsiTiVe!';
$lang['e15']='Wrong password! Only the guestbook owner may read this post!';
$lang['e16']='Wrong password! The selected entry wasn\'t deleted.';
$lang['e17']='You may only submit this guestbook once per session!';
$lang['e18']='Can\'t open entries file for reading! CHMOD this file to 666 (rw-rw-rw) and make sure your host allows PHP scripts to read from local files!';
$lang['e19']='Error';
$lang['e20']='ERROR: Invalid IP address, access blocked!';

// Text used
$lang['t07']='This post has been submitted from:';
$lang['t08']='Click here to continue';
$lang['t09']='View IP address';
$lang['t10']='Only guestbook owner may view IP addresses of people who posted into this guestbook. To view IP for the selected post please enter your administration password and click the &quot;View IP&quot; button.';
$lang['t12']='Your reply has been posted successfully!';
$lang['t13']='Reply to guestbook post';
$lang['t14']='Guestbook owner may use this form to reply to a post. To reply to the selected post please enter your administration password, your message and click the &quot;Post reply&quot; button.';
$lang['t15']='Submitted by';
$lang['t16']='Comments:';
$lang['t17']='Name:';
$lang['t18']='From:';
$lang['t19']='Website:';
$lang['t20']='E-mail:';
$lang['t21']='Administration password:';
$lang['t22']='Additional options:';
$lang['t23']='Ban this IP address';
$lang['t24']='View IP';
$lang['t25']='Your reply:';
$lang['t26']='More';
$lang['t27']='Contact';
$lang['t28']='Disable smileys';
$lang['t29']='Post reply';
$lang['t30']='Admin reply:';
$lang['t31']='Added:';
$lang['t32']='Delete this entry';
$lang['t33']='Reply to entry';
$lang['t34']='Back to Guestbook';
$lang['t35']='Read private post';
$lang['t36']='This is a private post and may only be read by the owner of this questbook. To view selected private post please enter your administration password and click the &quot;Read private post&quot; button.';
$lang['t37']='Selected entry was successfully removed!';
$lang['t38']='Delete guestbook post';
$lang['t39']='Only guestbook owner may delete posts. To delete selected post please enter your administration password and click the &quot;Delete this entry&quot; button to confirm your decision.';
$lang['t40']='Delete this entry';
$lang['t41']='Someone has just signed your guestbook'; // <-- This is e-mail subject
$lang['t42']='Hello!';
$lang['t43']='Someone has just signed your guestbook!'; // <-- This is text inside e-mail
$lang['t44']='Message (without smileys):';
$lang['t45']='Visit the below URL to view your guestbook:';
$lang['t46']='End of message';
$lang['t48']='Sign guestbook';
$lang['t49']='Required fields are <b>bold</b>.';
$lang['t50']='Your name:';
$lang['t51']='Where are you from?';
$lang['t52']='Your e-mail:';
$lang['t54']='Please do not enter URL addresses';
$lang['t55']='This is a private post, hide it from public.';
$lang['t56']='Please enter the number displayed above:';
$lang['t57']=' Add my comments ';
$lang['t58']='Private post. Click to view.';
$lang['t59']='Back to the previous page';
$lang['t60']='View guestbook';
$lang['t61']='Back to';
$lang['t62']='Security image';
$lang['t63']='View e-mail address';
$lang['t64']='Only guestbook owner may view e-mail addresses. To view contact e-mail please enter your administration password and click the &quot;View Email address&quot; button.';
$lang['t66']='E-mails are hidden from public';

// Added or modified in version 1.7
$lang['t01']='Number of entries: %d';
$lang['t75']='Number of pages: %d';
$lang['e01']='You cannot sign this guestbook at this time!';
$lang['t53']='Your Web site:';
$lang['t02']='&laquo; First';
$lang['t03']='&lsaquo; Prev';
$lang['t04']='Next &rsaquo;';
$lang['t05']='Last &raquo;';
$lang['t11']='&laquo; Guestbook main page';
$lang['t06']='No entries yet! Be the first one to sign this guestbook!';
$lang['t47']='Your message was added successfully!';
$lang['t67']='Please answer the anti-SPAM question';
$lang['t68']='Wrong answer to the anti-SPAM question';
$lang['t69']='Selected post was submitted from:';
$lang['t65']='Contact email for selected post:';
$lang['e21']='ERROR: We don\'t like spammers. You have been permanently banned from this guestbook!';
$lang['t70']='Emoticos';
$lang['t71']='Click on an icon to insert it into your message.';
$lang['t72']='Close Window';
$lang['t73']='Please limit comments to %d chars (now %d)';
$lang['t74']='Please keep number of emoticons below %d (now %d)';
$lang['m01']='January';
$lang['m02']='February';
$lang['m03']='March';
$lang['m04']='April';
$lang['m05']='May';
$lang['m06']='June';
$lang['m07']='July';
$lang['m08']='August';
$lang['m09']='September';
$lang['m10']='October';
$lang['m11']='November';
$lang['m12']='December';
$lang['e22']='Could not lock the entries file. Please try again later.';
$lang['t76']='Hello %s!';
$lang['t77']='The owner of %s has just replied to your entry.';
$lang['t78']='To read the reply please visit:';
$lang['t79']='Best regards,';
$lang['t80']='Reply to your Guestbook entry'; // E-mail subject
$lang['t81']='You already have a message waiting approval!';
$lang['e23']='Couldn\'t open temporary file for writing! Please CHMOD the apptmp folder to 777 (rwxrwxrwx)!';
$lang['t82']='A new guestbook entry is waiting approval.';
$lang['t83']='To APPROVE the entry visit this URL:';
$lang['t84']='To REJECT the entry visit this URL:';
$lang['t85']='Thank you. Your entry has been submitted for approval.';
$lang['e24']='Missing ID hash. Please make sure you copy the full approval/rejection URL!';
$lang['e25']='Wrong entry ID hash. Possible problems:<br><br>- the entry has already been approved or rejected<br>- you didn\'t copy the full approval/rejection URL';
$lang['t86']='The entry has been approved and submitted to the guestbook.';
$lang['t87']='The entry has been rejected and deleted.';

// Added 25th November 2009
$lang['t88']='Submit My Comment';

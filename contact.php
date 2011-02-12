<?php
/*
 * Template name: Kontakt
 */

if($_POST)
{
	if($_POST['contact-control'] != '')
	{
		$msg = array('error', 'Der opstod en fejl og din besked kunne ikke sendes. Prøv venligst senere, eller kontakt os på anden måde.');
	}
	else
	{
		if(!strpos($_POST['contact-email'], '@')) // Did they remember the @ in the email...
		{
			$msg = array('error', 'Udfyld venligst en gyldig email');
		}
		else
		{
			if(strlen(trim(strip_tags($_POST['contact-name']))) < 1)
			{
				$msg = array('error', 'Husk at skrive dit navn');
			}
			else
			{
				$name = $_POST['contact-name'];
				$email = $_POST['contact-email'];
				$subject = strip_tags($_POST['contact-subject']);
				$message = 'Emne: ' . $subject . "\n\nBesked: \n" .  strip_tags($_POST['contact-msg']) . "\n\nFra: $name ($email)";
				$headers = "From: " . $email;
				if(mail(get_bloginfo('admin_email'), 'Henvendelse fra ' . get_bloginfo('title'), $message, $headers))
				{
					$msg = array('ok', 'Din besked er sendt. Tak for din henvendelse.');
				}
			}
		}
	}
}

get_header();

if(have_posts())
{
	while(have_posts())
	{
		the_post();
?>
		<div class="post">
			<p class="<?php echo $msg[0]; ?>"><?php echo $msg[1]; ?></p>
			<h1><?php the_title(); ?></h1>
			<div>
				<?php the_content(); ?>
			</div>
			
			<div class="clear"></div>
			
			<div class="contact">
				<form action="<?php echo get_permalink($post -> ID); ?>" method="post">
					<div class="input">
						<label for="contact-name">Dit navn *</label>
						<input type="text" name="contact-name" id="contact-name" value="<?php echo $_POST['contact-name']; ?>" />
					</div>
					<div class="input">
						<label for="contact-name">Din e-mail *</label>
						<input type="text" name="contact-email" id="contact-email" value="<?php echo $_POST['contact-email']; ?>" />
					</div>
					<div class="input">
						<label for="contact-subject">Emne</label>
						<input type="text" name="contact-subject" id="contact-subject" value="<?php echo $_POST['contact-subject']; ?>" />
					</div>
					<div class="input">
						<label for="contact-name">Besked</label>
						<textarea name="contact-msg" id="contact-msg"><?php echo $_POST['contact-msg']; ?></textarea>
					</div>
					<div class="hidden">
						<label for="contact-control">Fyld ikke dette felt ud</label>
						<input type="text" name="contact-control" id="contact-control" />
					</div>
					<div class="input">
						<input type="submit" value="Send besked" />
					</div>
				</form>
				* skal udfyldes
			</div>
			
		</div>
<?php
	}
}

get_footer();

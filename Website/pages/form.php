<section id="form">
	<div>
		<ul class="tab-group">
			<li class="active">
				<button name="login" class="btn tab" onclick="activeTab(this);">Inloggen</button>
			</li>
			<li>		
				<button name="create_account" class="btn tab" onclick="activeTab(this);">Account aanmaken</button>
			</li>
		</ul>

		<div id="login" class="tab_content showTab">   
			<h2>Inloggen</h2>
			<span id="global_message"></span>
			<form id="login_form" name='Sign_in' onsubmit="return false;" method="post">
				<div class="field-wrap">
					<label>E-mailadres</label>
					<input type="email" name="email_login" onblur="validateField(this, false);" autocomplete="on"/>
					<span class="error_message"></span>
				</div>
				<div class="field-wrap">
					<label>Wachtwoord</label>
					<input type="password" name="wachtwoord_login" onblur="validateField(this, false);" autocomplete="off"/>
					<span class="error_message"></span>
				</div>
				<div class="button_container">
					<button type="button" name="forgot_password" onclick="activeTab(this);" class="btn insignificant">Wachtwoord vergeten?</button>
					<button type="submit" class="btn" onclick="validateForm(this);">Inloggen</button>
				</div>
			</form>
		</div>

		<div id="create_account" class="tab_content">
			<h2>Account aanmaken</h2>
			<form id="create_account_form" name='Create_Account' onsubmit="return false;" method="post">
				<div class="field-wrap double-row">
					<div class="double">
						<label>Voornaam</label>
						<input type="text" name="voornaam" onblur="validateField(this, false);" autocomplete="on"/>
						<span class="error_message"></span>
					</div>
					<div class="double">
						<label>Achternaam</label>
						<input type="text" name="achternaam" onblur="validateField(this, false);" autocomplete="on"/>
						<span class="error_message"></span>
					</div>
				</div>
				<div class="field-wrap">
					<label>E-mailadres</label>
					<input type="email" name="email_adres" onblur="validateField(this, false);" autocomplete="on"/>
					<span id="email_error" class="error_message"></span>
				</div>
				<div class="field-wrap double-row">
					<div class="double">
						<label>Wachtwoord</label>
						<input type="password" name="wachtwoord" onblur="validateField(this, false);" autocomplete="off"/>
						<span class="error_message"></span>
					</div>
					<div class="double">
						<label>Wachtwoord opnieuw</label>
						<input type="password" name="wachtwoord_bevestiging" onblur="validateField(this, false);" autocomplete="off"/>
						<span class="error_message"></span>
					</div>
					<span class="error_message"></span>
				</div>
				<div class="button_container">
					<button type="submit" class="btn" onclick="validateForm(this);">Account aanmaken</button>
				</div>
			</form>
		</div>

		<div id="forgot_password" class="tab_content">
			<h2>Wachtwoord herstelling</h2>
			<form id="password_recovery" name='password_recovery' onsubmit="return false;" method="post">
				<div class="field-wrap">
					<label>E-mailadres</label>
					<input type="email" name="password_recovery_email" onblur="validateField(this, false);" autocomplete="on"/>
					<span class="error_message"></span>
				</div>
				<div class="button_container">
					<button type="submit" class="btn" onclick="validateForm(this);">Wachtwoord herstellen</button>
				</div>
			</form>
		</div>
	</div>
</section>

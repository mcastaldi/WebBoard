		<header>
			<?php if($loggedInAsOrg) echo "<a href='createEvent.php'><button class='event'>Create Event</button></a>";?>
			<?php if($loggedInAsAdmin) echo "<a href='admin.php'><button class='event'>Administration</button></a>";?>
			<a href="index.php" id=logo>LTU Billboard</a>
			<?php if(!$loggedIn): ?>
			<span class="log-in">
				<button class="button" id="loginButton" data-toggle="modal" data-target="#loginModal">Log In</button>&nbsp;&nbsp;&nbsp;&nbsp;
				<br />
				<a id="createAccountLink">or create an account</a>
			</span>
			<?php else: ?>
			<span class="log-in">
				<form action="logout.php" method="post" role="form">
					<div class="loggedInText" align="right">Logged in as:<br /> <?php echo ($loggedInAsOrg ? "<a href='organizations.php'>{$orgInfo['name']}</a>" : "<a href='accountSettings.php'>{$userInfo['firstName']}</a>");?></div>
					<button class="button" id="logoutButton" type="submit" name="source" value="<?php echo $thisPage;?>">Log Out</button>
				</form>
			</span>
			<?php endif ?>
			<br />
			<br />
			<br />

		</header>
		<?php if(!$loggedInAsOrg && !$loggedInAsUser): ?>
		<!-- Modal -->
		<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<!-- Modal header with close button and title-->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h2 class="modal-title" id="loginModal">Log-In or Create Account</h2>
					</div>

					<!-- Modal body -->
					<div class="modal-body">

						<!-- Tabs for either log-in or account creation -->
						<ul id="loginTabs" class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#loginAsStudent" aria-controls="loginAsStudent" role="tab" data-toggle="tab">Log-In As Student</a></li>
							<li role="presentation"><a href="#loginAsOrg" aria-controls="loginAsOrg" role="tab" data-toggle="tab">Log-In As Organization</a></li>
							<li role="presentation"><a href="#createAccount" aria-controls="createAccount" role="tab" data-toggle="tab">Create Account</a></li>
						</ul>

						<!-- Div for tab content -->
						<div class="tab-content">
							<!-- First tab for student log-in form -->
							<div role="tabpanel" class="tab-pane active" id="loginAsStudent"><br />
							
								
								<!--Form for logging in. Just takes username, password -->
								<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="studentForm">
									<!-- Username row -->
									<div class="form-group row">
										<label for="studentEmail" class="col-sm-4 form-control-label" align="right">Email</label>
										<div class="col-sm-7">
											<input required type="email" class="form-control" id="studentEmail" name="studentEmail" placeholder="user@example.com" />
											
										</div>
									</div>
									<!-- Password row -->
									<div class="form-group row">
										<label for="studentPassword" class="col-sm-4 form-control-label" align="right">Password</label>
										<div class="col-sm-5">
											<input required type="password" class="form-control" id="studentPassword" name="studentPassword" placeholder="Password" />
										</div>
									</div>
									<!-- Submit button -->
									<hr />
									<div class="form-group row">
										<div class="col-sm-12" align="right">
											<label id="stuLoginMessage" class="message" for="type"></label>
											<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="stu">Submit</button>
											<button type = "submit" class ="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</div>
								</form>
								<!-- End of log-in form and 1st tab -->
							</div>

							<!-- Second tab for Organization log-in form -->
							<div role="tabpanel" class="tab-pane" id="loginAsOrg"><br />
								<!--Form for logging in. Just takes username, password -->
								<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="orgForm">
									<!-- Username row -->
									<div class="form-group row">
										<label for="orgEmail" class="col-sm-4 form-control-label" align="right">Email</label>
										<div class="col-sm-7">
											<input required type="email" class="form-control" id="orgEmail" name ="orgEmail" placeholder="org@example.com" />
										</div>
									</div>
									<!-- Password row -->
									<div class="form-group row">
										<label for="orgPassword" class="col-sm-4 form-control-label" align="right">Password</label>
										<div class="col-sm-5">
											<input required type="password" class="form-control" id="orgPassword" name="orgPassword" placeholder="Password" />
										</div>
									</div>
									<!-- Submit button -->
									<hr />
									<div class="form-group row">
										<div class="col-sm-12" align="right">
											<label id="orgLoginMessage" class="message" for="type"></label>
											<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="org">Submit</button>
											<button type = "submit" class ="btn btn-default" data-dismiss="modal">Cancel</button>
										</div>
									</div>
								</form>
								<!-- End of log-in form and 2nd tab -->
							</div>
							<!-- Third tab for account creation form -->
							<div role="tabpanel" class="tab-pane" id="createAccount"><br />
								<!--Form for account creation. Just takes username, password twice, and account type -->
								<form id="chooseActType">
									<!-- Radio button row. -->
									<div class="form-group row">
										<label class="col-sm-3" align="right">Account Type</label>
										<div class="col-sm-5">
											<div class="radio-inline">
												<label><input type="radio" name="actType" value="studentAct" checked />Student</label>
											</div>
											<div class="radio-inline">
												<label><input type="radio" name="actType" value="orgAct" />Organization</label>
											</div>
										</div>
									</div>
								</form>
								<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="createStuAct">
									<div id="studentAct" class="chooseActType">
										<!-- First Name Row -->
										<div class="form-group row">
											<label for="firstName" class="col-sm-3 form-control-label" align="right">First Name</label>
											<div class="col-sm-3">
												<input required type="name" class="form-control" id="firstName" name="firstName" placeholder="John" />
											</div>
										</div>
										<div class="form-group row">
											<!-- Last Name Row -->
											<label for="lastName" class="col-sm-3 form-control-label" align="right">Last Name</label>
											<div class="col-sm-4">
												<input required type="name" class="form-control" id="lastName" name="lastName" placeholder="Doe" />
											</div>
										</div>
										<div class="form-group row">
											<!-- Email row -->
											<label for="stuEmail" class="col-sm-3 form-control-label" align="right">Email</label>
											<div class="col-sm-4">
												<input required type="email" class="form-control" id="stuEmail" name="stuEmail" placeholder="name@example.com" />
											</div>
										</div>
										<!-- Password row -->
										<div class="form-group row">
											<label for="stuCreatePassword" class="col-sm-3 form-control-label" align="right">Password</label>
											<div class="col-sm-4">
												<input required type="password" class="form-control" id="stuCreatePassword" name="stuCreatePassword" placeholder="Password" />
											</div>
										</div>
										<div class="form-group row">
											<!-- Confirm password row -->
											<label for="confirmStuPassword" class="col-sm-3 form-control-label" align="right">Repeat Password</label>
											<div class="col-sm-4">
												<input required type="password" class="form-control" id="confirmStuPassword" name="confirmStuPassword" placeholder="Password" />
											</div>
										</div>
										<hr />
										<div class="form-group row">
											<!-- Submit Row -->
											<div class="col-sm-12" align="right">
												<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="stuCreate">Submit</button>
												<button type = "submit" class ="btn btn-default" data-dismiss="modal">Cancel</button>
											</div>
										</div>
									</div>
								</form>
								<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="createOrgAct">
									<div id="orgAct" class="chooseActType">
										<!-- Email row -->
										<div class="form-group row">
											<label for="orgName" class="col-sm-3 form-control-label" align="right">Organization Name</label>
											<div class="col-sm-4">
												<input required type="name" class="form-control" id="orgName" name="orgName" placeholder="Example Name" />
											</div>
										</div>
										<div class="form-group row">
											<label for="orgEmail" class="col-sm-3 form-control-label" align="right">Email</label>
											<div class="col-sm-4">
												<input required type="email" class="form-control" id="orgEmail" name="orgEmail" placeholder="org@example.com" />
											</div>
										</div>
										<!-- Password row -->
										<div class="form-group row">
											<label for="orgPassword" class="col-sm-3 form-control-label" align="right">Password</label>
											<div class="col-sm-4">
												<input required type="password" class="form-control" id="orgCreatePassword" name="orgCreatePassword" placeholder="Password" />
											</div>
										</div>
										<!-- Confirm password row -->
										<div class="form-group row">
											<label for="confirmOrgPassword" class="col-sm-3 form-control-label" align="right">Repeat Password</label>
											<div class="col-sm-4">
												<input required type="password" class="form-control" id="confirmOrgPassword" name="confirmOrgPassword" placeholder="Password" />
											</div>
										</div>
										<div class="form-group row">
											<label for="orgDesc" class="col-sm-3 form-control-label" align="right">Description</label>
											<div class="col-sm-6">
												<textarea required class="form-control" id="orgDesc" name="orgDesc" placeholder="Detailed description of your organization"></textarea>
											</div>
										</div>
										<div class="form-group row">
											<label for="orgUrl" class="col-sm-3 form-control-label" align="right">Website</label>
											<div class="col-sm-4">
												<input required type="text" class="form-control" id="orgUrl" name="orgUrl" placeholder="Example.com" />
											</div>
										</div>
										<hr />
										<div class="form-group row">
											<div class="col-sm-12" align="right">
												<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="orgCreate">Submit</button>
												<button type = "submit" class ="btn btn-default" data-dismiss="modal">Cancel</button>
											</div>
										</div>
									</div>
								</form>
								<!-- End of account creation form and third tab -->
							</div>



						</div>
					</div>

				</div>
			</div>
		</div>
		<?php endif ?>
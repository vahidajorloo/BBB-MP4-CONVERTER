<?php
if (isset($_POST['delete-meeting'])) {
	$meeting = explode(".", $_POST['delete-meeting']);
	if ($meeting[1] == "mp4" && !str_contains($meeting[0], "/")) {
		if (function_exists('shell_exec')) {
			$output = shell_exec('rm '.$_POST['delete-meeting']);
		} else {
			echo "shell_exec() is disabled.";
		}
	}
	die();
}
if (isset($_POST['meeting-id'])) {
	if (function_exists('shell_exec')) {
		$output = shell_exec('/var/www/bbb-mp4/bbb-mp4.sh '.$_POST['meeting-id'] .' &');
		$success = '
			<section id="toast" class="info">
			  <div id="icon-wrapper">
				<div id="icon"></div>
			  </div>
			  <div id="toast-message">
				<h4>موفق</h4>
				<p>فایل میتینگ موردنظر با موفقیت در صف تبدیل قرار گرفت.</p>
			  </div>
			  <button id="toast-close"></button>
			  <div id="timer"></div>
			</section>
			<script>
				const toast = document.querySelector("#toast");
				const toastTimer = document.querySelector("#timer");
				const closeToastBtn = document.querySelector("#toast-close");
				let countdown;
				const closeToast = () => {
				  toast.style.animation = "close 0.3s cubic-bezier(.87,-1,.57,.97) forwards";
				  toastTimer.classList.remove("timer-animation");
				  clearTimeout(countdown)
				}
				const openToast = (type) => {
				  toast.classList = [type];
				  toast.style.animation = "open 0.3s cubic-bezier(.47,.02,.44,2) forwards";
				  toastTimer.classList.add("timer-animation");
				  clearTimeout(countdown)
				  countdown = setTimeout(() => {
					closeToast();
				  }, 5000)
				}
				closeToastBtn.addEventListener("click", closeToast);
				openToast("success");
			</script>
		';
	} else {
		$success = '';
		echo "shell_exec() is disabled.";
	}
}
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return $files;
}
$files = preg_grep('~\.(mp4)$~', scan_dir("."));
?>


<!DOCTYPE html>
<html lang="fa">
	<head>
		<title>تبدیل رکورد</title>
		<link rel="stylesheet" type="text/css" href="./style.css">
		<script>
			if ( window.history.replaceState ) {
				window.history.replaceState( null, null, window.location.href );
			}
			function deleteMeeting(meeting) {
				var retVal = confirm("فایل "+meeting+" حذف خواهد شد. آیا مطمئنید؟");
				if( retVal == true ) {
					var xhr = new XMLHttpRequest();
					xhr.open('POST', '', true);
					xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
					xhr.onload = function (e) {
						location.reload();
					};
					xhr.send('delete-meeting='+meeting);
				}
			 }
		</script>
	</head>
	<body>
		<div class="main">
			<h1>تبدیل رکورد</h1>
			<p>برای تبدیل فایل جلسه آنلاین لطفا آیدی میتینگ را در کادر زیر وارد کرده و دکمه تبدیل را بزنید. پس از اتمام عملیات تبدیل، فایل شما در لیست ظاهر خواهد شد.</p>
			<form method="post" action="">
				<div class="input-wrapper">
					<label for="meeting-id">آیدی میتینگ</label>
					<input type="text" size="50" name="meeting-id" id="meeting-id">
				</div>
				<input type="submit" name="submit" value="تبدیل">
			</form>
		</div>
		<div class="main list">
			<h3>لیست فایل‌ها</h3>
			<?php
				if(empty($files)) {
					echo '<p>لیست فایل‌های شما خالی می‌باشد.</p>';
				} else {
					echo '<table><thead><th>میتینگ</th><th>عملیات</th></thead><tbody>';
					foreach($files as $file) {
						$meeting = "'".$file."'";
						echo '<tr><td>'.$file.'</td><td>';
						echo '<span><a style="color:#17AA8D" href="/recording/'.$file.'" target="_blank" download>دانلود</a></span>';
						echo '<span><a style="color:#F00" href="javascript:void(0)" onClick="deleteMeeting('.$meeting.')">حذف</a></span>';
						echo '</td></tr>';
					}
					echo '</tbody></table>';
				}
			?>
		</div>
		<?php echo $success; ?>
	</body>
</html>

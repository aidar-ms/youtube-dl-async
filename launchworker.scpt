tell application "Terminal" 
	activate
	tell application "System Events"
		keystroke "t" using {command down}
	end tell
	do script "php yii youtube-downloader/prepare-routine" in selected tab of the front window
end tell

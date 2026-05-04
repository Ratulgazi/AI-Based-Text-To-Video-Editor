<!DOCTYPE html>
<html>
<head>
    <title>Your Video</title>
</head>
<body>
    <h1>Here’s Your Video 🎬</h1>
    <video width="640" controls>
        <source src="{{ asset($videoPath) }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <br>
    <a href="{{ asset($videoPath) }}" download>Download Video</a>
</body>
</html>
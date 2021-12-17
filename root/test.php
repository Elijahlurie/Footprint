<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Test</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
<style>
body{
  margin:0;
  padding:0;
}
.parallax {
  perspective: 1px;
  height: 100vh;
  overflow-x: hidden;
  overflow-y: auto;
}
.parallax__group {
  position: relative;
  height: 500px;
  transform-style: preserve-3d;
  border: 5px solid black;
}
.parallax__layer {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.parallax__layer--base {
  border: 5px solid red;
  display:flex;
  align-items:center;

}
.parallax__layer--back {
  transform: translateZ(-1px) scale(2);
  background-image: url('images/dunes.jpg');
  color:blue;
  display:flex;
  align-items:center;
}



.section{
  background: green;
  height: 300px;
  display:flex;
  align-items:center;

}
.two{
  background: blue;
}
</style>
</head>

<body>

  <div class="parallax">
    <div class="parallax__group">
      <div class="parallax__layer parallax__layer--back">
        <h2>This is the background</h2>
      </div>
      <div class="parallax__layer parallax__layer--base">
        <h3>this is the foreground.</h3>
      </div>
    </div>
    <div class="parallax__group">
        <div class="section">
          <h1>heres a little section</h1>
          <p>ldf;aldjfs;aldfj</p>
        </div>
        <div class="section two">
          <h1>heres a little section</h1>
          <p>ldf;aldjfs;aldfj</p>
        </div>
      </div>
    </div>

</body>
</html>

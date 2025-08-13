<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAT 2025 Mock Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://static.thingsapp.co/catmocktest-static/app_v202508031147.css">
<style type="text/css">

.educards-cont { width:100%; }
.educard { 
    width: 90vh;
    height: 90vh;
    max-height: 600px;
    max-width: 600px;
    min-width: 400px;
    min-height: 400px;
    perspective: 1000px;
    margin: 16px auto;
  }

  .educard .cardView{
    width:100%;
    position: relative;
  width: 100%;
  height: 100%;
  text-align: center;
  transition: transform 0.6s;
  transform-style: preserve-3d;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  }

/*   .educard:hover .cardView {
  transform: rotateY(180deg);
} 
*/

  .educard .frontView,.backView{
    width:90vh;
    height:90vh;
    max-height: 600px;
    max-width: 600px;
/*    margin: 16px auto;*/
    border: 2px solid black;
    border-radius: 8px;
    box-shadow: 10px 10px #000000;
    min-width:400px;
    min-height:400px;
    position: absolute;
  /* width: 100%;
  height: 100%; */
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  }

  .educard .backView{
      transform: rotateY(180deg);
  }

  .educard .cardHeader{
    display: flex;
    padding: 8px 16px;
    flex-wrap: wrap;
    justify-content: end;
  }

  .educard .cardHeader img{
    height: 50px;
    transform: scaleX(-1);
  }

  .educard .cardContent{
    border: 0px solid blue;
    height: calc(100% - 146px);
    position: relative; 
  }

  .educard .cardFooter{
    height: 80px;
    text-align:center;
  }

  .educard .cardFooterHeading{
    font-size: bold;
    text-align:center;
  }

  .educard .cardFooterButtons{
    height: 50px;
    display:flex;
    flex-wrap: wrap;
    justify-content: center;
    column-gap:16px;
  }

  .educard .cardText{
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width:100%;
    padding:16px;
  }

  .educard .button{
    padding: 4px;
    min-height: 40px;
    font-size: 1.2rem;
    min-width: 70px;
  }

    </style>
</head>
<body>
<div>
  <div>
    <div class="educards-cont">
        <div class="educard">
            <div class="cardView">
                <div class="frontView">
                  <div class="cardHeader"><img src="https://cdn.shopify.com/s/files/1/0768/7110/6796/files/Lanka_800_x_800_px_6_b3b0fa02-f031-4d5b-a181-1f207e16ff05.png?v=1749910700" /></div>
                  <div class="cardContent">
                    <div class="cardText" id="front1">Front</div>
                  </div>
                  <div class="cardFooter">
                    <div style="padding:16px; font-weight:bold;">
                      Check in 20 secs
                      <a href="javascript:flipCard(this);" class="button">Show</a>
                    </div>
                  </div>
                </div>
                <div class="backView">
                  <div class="cardHeader"><img src="https://cdn.shopify.com/s/files/1/0768/7110/6796/files/Lanka_800_x_800_px_6_b3b0fa02-f031-4d5b-a181-1f207e16ff05.png?v=1749910700" /></div>
                  <div class="cardContent">
                    <div class="cardText" id="back1">Back</div>
                  </div>
                  <div class="cardFooter">
                    <div class="cardFooterHeading">Did you get It Right</div>
                    <div class="cardFooterButtons">
                      <div>
                      <a href="/products/cat-mock-test-2025" class="button">Yes</a>
                      </div>
                      <div>
                      <a href="/products/cat-mock-test-2025" class="button">Kind Of</a>
                      </div>
                      <div>
                      <a href="/products/cat-mock-test-2025" class="button">No</a>
                      </div>
                    </div>  
                  </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript">
//document.querySelectorAll('')


    function flipCard(elm){
        console.log("sjsjjsjsjsjjsjsjsjsj")
        console.log(elm)
        elm.style.backgroundColor = 'blue';
    }
</script>
</body>
</html>
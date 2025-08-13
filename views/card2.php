<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Card Flip with Timer</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f0f0f0;
    /*display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;*/
  }

  .educards-cont {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .educard{
    width:90vh;
    height:90vh;
    max-height: 600px;
    max-width: 600px;
    min-width:400px;
    min-height:400px;
    perspective: 1000px;
    position: relative;
  }

  .educard .cardView{
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s;
    transform-style: preserve-3d;
  }

  .card.flip {
    transform: rotateY(180deg);
  }

  .card-side {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    overflow: hidden;
    border: 2px solid black;
    border-radius: 8px;
    box-shadow: 10px 10px #000000;
  }

  .front {
    background: #ffffff;
  }

  .timer {
    font-size: 1.1rem;
    color: #333;
  }

  .back {
    background: #ffffff;
    transform: rotateY(180deg);
  }

  .next-btn {
    background: white;
    color: #4CAF50;
    border: none;
    padding: 10px 15px;
    margin-top: 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
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

<div class="educards-cont">
    <div class="educard" id="card-container">
      <!-- Cards will be injected dynamically -->
    </div>
</div>
<script>
const cardsData = [
  { front: "Front of Card 1", back: "Back of Card 1" },
  { front: "Front of Card 2", back: "Back of Card 2" },
  { front: "Front of Card 3", back: "Back of Card 3" }
];

let currentCardIndex = 0;
let timerInterval;
let countdown = 20;

function createCard(frontText, backText) {
  const cardEl = document.createElement("div");
  cardEl.className = "card educard cardView";

  const frontEl = document.createElement("div");
     var frontHtml = `
        <div class="cardHeader">
            <img src="https://cdn.shopify.com/s/files/1/0768/7110/6796/files/Lanka_800_x_800_px_6_b3b0fa02-f031-4d5b-a181-1f207e16ff05.png?v=1749910700" />
        </div>
        <div class="cardContent">
            <div class="cardText">${frontText}</div>
        </div>
        <div class="cardFooter">
            <div style="padding:16px; font-weight:bold;">Check in <span id="timer">${countdown}</span>secs</div>
        </div>
    `;

  frontEl.className = "card-side front";

  frontEl.innerHTML = frontHtml

  var backHtml = `
        <div class="cardHeader">
            <img src="https://cdn.shopify.com/s/files/1/0768/7110/6796/files/Lanka_800_x_800_px_6_b3b0fa02-f031-4d5b-a181-1f207e16ff05.png?v=1749910700" />
        </div>
        <div class="cardContent">
            <div class="cardText" id="back1">Back</div>
        </div>
        <div class="cardFooter">
            <div class="cardFooterHeading">Did you get It Right</div>
            <div class="cardFooterButtons">
                <div><a href="javascript:actionOnCard(1);" class="button">Yes</a></div>
                <div><a href="javascript:actionOnCard(3);" class="button">Kind Of</a></div>
                <div><a href="javascript:actionOnCard(2);" class="button">No</a></div>
            </div>  
        </div>
  `;

  // frontEl.innerHTML = `
  //   <div>${frontText}</div>
  //   <div class="timer">Time left: <span id="timer">${countdown}</span>s</div>
  // `;

  const backEl = document.createElement("div");
  backEl.className = "card-side back";
  backEl.innerHTML = backHtml
  // backEl.innerHTML = `
  //   <div>${backText}</div>
  //   <button class="next-btn">Next Card</button>
  // `;

  cardEl.appendChild(frontEl);
  cardEl.appendChild(backEl);

  return cardEl;
}

function startTimer(cardEl) {
  let timeLeft = countdown;
  const timerEl = cardEl.querySelector("#timer");

  timerInterval = setInterval(() => {
    timeLeft--;
    if (timerEl) timerEl.textContent = timeLeft;
    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      cardEl.classList.add("flip");
    }
  }, 1000);
}

function showCard(index) {
  const container = document.getElementById("card-container");
  container.innerHTML = "";

  if (index >= cardsData.length) {
    container.innerHTML = "<h2>All cards completed!</h2>";
    return;
  }

  const card = createCard(cardsData[index].front, cardsData[index].back);
  container.appendChild(card);

  startTimer(card);

  // Next button
  // card.querySelector(".next-btn").addEventListener("click", () => {
  //   clearInterval(timerInterval);
  //   currentCardIndex++;
  //   showCard(currentCardIndex);
  // });
}

function actionOnCard(action){

}

showCard(currentCardIndex);
</script>

</body>
</html>

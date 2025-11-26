<section class="match-section">
  <h2>💡 Potrivire inteligentă</h2>
  <p>Răspunde sincer la câteva întrebări și află ce prieten necuvântător ți se potrivește cel mai bine!</p>

  <form method="post" action="/site/public/index.php?controller=match&action=calculate" class="match-form">

    <div class="question">
      <h3>1️⃣ Ce stil de viață ai?</h3>
      <label><input type="radio" name="lifestyle" value="active" required> Activ — îmi place să ies des afară, sport, plimbări</label>
      <label><input type="radio" name="lifestyle" value="moderate"> Echilibrat — îmi place și mișcarea, dar și relaxarea</label>
      <label><input type="radio" name="lifestyle" value="relaxed"> Relaxat — prefer liniștea, filmele și timpul acasă</label>
    </div>

    <div class="question">
      <h3>2️⃣ Unde locuiești?</h3>
      <label><input type="radio" name="space" value="small" required> Apartament mic</label>
      <label><input type="radio" name="space" value="medium"> Apartament mediu</label>
      <label><input type="radio" name="space" value="large"> Casă cu curte</label>
    </div>

    <div class="question">
      <h3>3️⃣ Cât timp liber ai zilnic pentru animal?</h3>
      <label><input type="radio" name="time" value="low" required> Puțin (sub 1 oră)</label>
      <label><input type="radio" name="time" value="medium"> Moderat (1–3 ore)</label>
      <label><input type="radio" name="time" value="high"> Mult (peste 3 ore)</label>
    </div>

    <div class="question">
      <h3>4️⃣ Câtă experiență ai cu animalele?</h3>
      <label><input type="radio" name="experience" value="none" required> Nicio experiență — ar fi primul meu animal</label>
      <label><input type="radio" name="experience" value="some"> Am mai avut animale înainte</label>
      <label><input type="radio" name="experience" value="a_lot"> Sunt obișnuit/ă să am grijă de animale</label>
    </div>

    <div class="question">
      <h3>5️⃣ Ce temperament preferi la un animal?</h3>
      <label><input type="radio" name="temperament" value="calm" required> Calm și liniștit</label>
      <label><input type="radio" name="temperament" value="playful"> Jucăuș și energic</label>
      <label><input type="radio" name="temperament" value="independent"> Independent și curajos</label>
    </div>

    <button type="submit" class="button">🔍 Află potrivirea</button>
  </form>
</section>

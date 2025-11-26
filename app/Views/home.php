<section class="home-page">

  <div class="intro-container">
    <div class="slideshow-container left">
      <div class="slide fade active">
  <img src="<?= htmlspecialchars(upload_url('Catel1.jpg')) ?>" alt="Cățel fericit">
        <div class="caption">Buddy – adoptat din Cluj ❤️</div>
      </div>
      <div class="slide fade">
  <img src="<?= htmlspecialchars(upload_url('Pongo.jpg')) ?>" alt="Pisică iubitoare">
        <div class="caption">Pongo – o nouă familie 🏡</div>
      </div>
      <div class="slide fade">
  <img src="<?= htmlspecialchars(upload_url('iepuras.jpg')) ?>" alt="Iepuraș">
        <div class="caption">Coco – iubit de toți 🐰</div>
      </div>
    </div>

    <div class="hero">
      <h1>Bun venit pe <span>PawMatch!</span> 🐾</h1>
      <p>Găsește-ți prietenul perfect</p>
      <a href="/site/public/index.php?controller=animal&action=index" class="button">
        Vezi animalele disponibile
      </a>
    </div>

    <div class="slideshow-container right">
      <div class="slide2 fade active">
  <img src="<?= htmlspecialchars(upload_url('Blue.jpg')) ?>" alt="Cățel drăguț">
        <div class="caption">Blue – jucăuș și loial 🐶</div>
      </div>
      <div class="slide2 fade">
  <img src="<?= htmlspecialchars(upload_url('PisicaNeagra.jpg')) ?>" alt="Pisică blândă">
        <div class="caption">Mia – adorabilă și curioasă 🐱</div>
      </div>
      <div class="slide2 fade">
  <img src="<?= htmlspecialchars(upload_url('hamster.jpg')) ?>" alt="Hamster">
        <div class="caption">Nibbles – mic dar curajos 🐹</div>
      </div>
    </div>
  </div>

  <div class="features-row">
    <div class="feature">
      <h3>💡 Potrivire inteligentă</h3>
      <p>Descoperă animalele compatibile cu personalitatea ta.</p>
    </div>
    <div class="feature">
      <h3>📸 Adaugă anunțuri</h3>
      <p>Publică animale spre adopție și oferă-le o nouă șansă.</p>
    </div>
    <div class="feature">
      <h3>🏡 Povești fericite</h3>
      <p>Vezi povești reale despre adopții reușite prin PawMatch.</p>
    </div>
  </div>

  <style>
  .home-page {
    margin-top: -50px;
  }

  .intro-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
    max-width: 1200px;
    margin: 10px auto 25px auto;
  }

  .slideshow-container {
    position: relative;
    width: 30%;
    min-width: 270px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    background: #000;
  }

  .slide, .slide2 {
    display: none;
    position: relative;
  }

  .slide img, .slide2 img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    display: block;
  }

  .caption {
    position: absolute;
    bottom: 8px;
    left: 15px;
    color: #fff;
    font-weight: bold;
    background: rgba(0,0,0,0.5);
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.9rem;
  }

  .hero {
    width: 35%;
    text-align: center;
  }

  .hero h1 {
    font-size: 1.9rem;
    color: #4a2e00;
    margin-bottom: 8px;
  }

  .hero span {
    color: #fcbf49;
  }

  .hero p {
    color: #6b4f1d;
    font-size: 1.05rem;
    margin-bottom: 15px;
  }

  .hero .button {
    background-color: #fcbf49;
    color: #4a2e00;
    padding: 9px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }

  .hero .button:hover {
    background-color: #f9a825;
  }

  .features-row {
    display: flex;
    justify-content: center;
    gap: 25px;
    flex-wrap: nowrap;
    max-width: 1000px;
    margin: 0 auto 40px auto;
  }

  .feature {
    background: #fffaf0;
    border-radius: 16px;
    padding: 22px;
    width: 30%;
    min-width: 250px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s;
  }

  .feature:hover {
    transform: translateY(-5px);
  }

  .feature h3 {
    color: #5b3b00;
    margin-bottom: 8px;
  }

  .feature p {
    color: #6b4f1d;
    font-size: 0.95rem;
  }

  @media (max-width: 950px) {
    .intro-container {
      flex-direction: column;
      text-align: center;
      margin: 0 auto;
    }

    .slideshow-container {
      width: 90%;
    }

    .hero {
      width: 100%;
    }

    .features-row {
      flex-wrap: wrap;
      gap: 15px;
    }

    .feature {
      width: 80%;
    }
  }
  </style>

  <script>
  (function () {
    try {
      const slidesA = document.getElementsByClassName("slide");
      console.log('Slideshow A elements found:', slidesA.length);
      if (slidesA && slidesA.length > 0) {
        let idxA = 0;
        const showA = function () {
          for (let i = 0; i < slidesA.length; i++) slidesA[i].style.setProperty('display', 'none', 'important');
          idxA++;
          if (idxA > slidesA.length) idxA = 1;
          if (slidesA[idxA - 1]) slidesA[idxA - 1].style.setProperty('display', 'block', 'important');
          setTimeout(showA, 4000);
        };
        showA();
      }

      const slidesB = document.getElementsByClassName("slide2");
      console.log('Slideshow B elements found:', slidesB.length);
      if (slidesB && slidesB.length > 0) {
        let idxB = 0;
        const showB = function () {
          for (let i = 0; i < slidesB.length; i++) slidesB[i].style.setProperty('display', 'none', 'important');
          idxB++;
          if (idxB > slidesB.length) idxB = 1;
          if (slidesB[idxB - 1]) slidesB[idxB - 1].style.setProperty('display', 'block', 'important');
          setTimeout(showB, 5000);
        };
        showB();
      }
    } catch (e) {
      console.error('Slideshow error:', e);
    }
  })();
  </script>
</section>

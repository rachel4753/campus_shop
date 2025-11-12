// === MODALE PROFIL ===
document.addEventListener('DOMContentLoaded', function() {
    const profilBtn = document.getElementById('profilBtn');
    const modalProfil = document.getElementById('modalProfil');
    const closeProfil = document.getElementById('closeProfil');
    const showLogin = document.getElementById('showLogin');
    const showRegister = document.getElementById('showRegister');
    const formInscription = document.getElementById('formInscription');
    const formConnexion = document.getElementById('formConnexion');
    const roleSelect = document.getElementById('roleSelect');
    const livreurFields = document.getElementById('livreurFields');
    if (profilBtn && modalProfil && closeProfil) {
        profilBtn.addEventListener('click', function() {
            modalProfil.classList.add('active');
        });
        closeProfil.addEventListener('click', function() {
            modalProfil.classList.remove('active');
        });
        modalProfil.querySelector('.modal-overlay').addEventListener('click', function() {
            modalProfil.classList.remove('active');
        });
    }
    if (showLogin && showRegister && formInscription && formConnexion) {
        showLogin.addEventListener('click', function() {
            formInscription.style.display = 'none';
            formConnexion.style.display = 'block';
        });
        showRegister.addEventListener('click', function() {
            formConnexion.style.display = 'none';
            formInscription.style.display = 'block';
        });
    }
    if (roleSelect && livreurFields) {
        roleSelect.addEventListener('change', function() {
            if (roleSelect.value === 'livreur') {
                livreurFields.style.display = 'flex';
            } else {
                livreurFields.style.display = 'none';
            }
        });
    }
});
// === MODALE PANIER ===
document.addEventListener('DOMContentLoaded', function() {
    const panierBtn = document.getElementById('panierBtn');
    const modalPanier = document.getElementById('modalPanier');
    const closePanier = document.getElementById('closePanier');
    if (panierBtn && modalPanier && closePanier) {
        panierBtn.addEventListener('click', function() {
            modalPanier.classList.add('active');
        });
        closePanier.addEventListener('click', function() {
            modalPanier.classList.remove('active');
        });
        modalPanier.querySelector('.modal-overlay').addEventListener('click', function() {
            modalPanier.classList.remove('active');
        });
    }
});
/* JS pour Campus Shop - séparé depuis index.php
   Encapsulé dans DOMContentLoaded et conçu pour fonctionner sans frameworks.
*/

document.addEventListener('DOMContentLoaded', function() {
    // ======= Données et configuration =======
    window.produits = []; // exposé globalement si nécessaire
    // Suppression panier
    let categories = [];

    // Configuration du tri et filtrage
    const PRODUITS_PAR_PAGE = 35;
    const CRITERES_TRI = {
        POPULAIRE: 'populaire',
        NOUVEAU: 'nouveau',
        PRIX_ASC: 'prix-asc',
        PRIX_DESC: 'prix-desc',
        MIEUX_NOTE: 'note'
    };

    // État
    let triActuel = CRITERES_TRI.POPULAIRE;
    let pageActuelle = 1;
    let categorieFiltree = 'all';

    const grilleProduits = document.getElementById('grilleProduits');
    // Suppression compteurPanier
    const affichageProduits = document.getElementById('affichageProduits');
    const pagination = document.getElementById('pagination');

    // Tri flottant
    const triFlottant = document.getElementById('triFlottant');
    if (triFlottant) {
        triFlottant.querySelectorAll('.tri-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                triFlottant.querySelectorAll('.tri-btn').forEach(b => b.classList.remove('active-tri'));
                btn.classList.add('active-tri');
                triActuel = btn.getAttribute('data-tri');
                changerPage(1);
            });
        });
    }

    function trierProduits(produitsList, critere) {
        switch (critere) {
            case CRITERES_TRI.POPULAIRE:
                return [...produitsList].sort((a, b) => (b.vues || 0) - (a.vues || 0));
            case CRITERES_TRI.NOUVEAU:
                return [...produitsList].sort((a, b) => new Date(b.dateAjout) - new Date(a.dateAjout));
            case CRITERES_TRI.PRIX_ASC:
                return [...produitsList].sort((a, b) => a.prix - b.prix);
            case CRITERES_TRI.PRIX_DESC:
                return [...produitsList].sort((a, b) => b.prix - a.prix);
            case CRITERES_TRI.MIEUX_NOTE:
                return [...produitsList].sort((a, b) => (b.note || 0) - (a.note || 0));
            default:
                return produitsList;
        }
    }

    function filtrerParCategorie(produitsList, categorie) {
        if (categorie === 'all') return produitsList;
        return produitsList.filter(p => p.categorie == categorie);
    }

    function getProduitsSemaine() {
        const uneSemaine = 7 * 24 * 60 * 60 * 1000;
        const maintenant = new Date();
        return produits.filter(p => {
            const dateAjout = new Date(p.dateAjout);
            return maintenant - dateAjout <= uneSemaine;
        });
    }

    // Affichage
    function afficherProduits(liste) {
        if (!grilleProduits) return;
        grilleProduits.innerHTML = '';
        if (!liste || liste.length === 0) {
            grilleProduits.innerHTML = "<p class='placeholder'>Aucun produit pour l'instant. Les utilisateurs ajouteront leurs articles ici.</p>";
        } else {
            liste.forEach((prod, index) => {
                const card = document.createElement('div');
                card.className = 'card produit-card';
                card.innerHTML = `\n          <img src="${prod.image || 'https://via.placeholder.com/400x300'}" alt="${prod.nom || ''}" loading="lazy">\n          <h4>${prod.nom || ''}</h4>\n          <p>${prod.description || ''}</p>\n          <p><strong>${prod.prix || 0} FCFA</strong></p>\n        `;
                grilleProduits.appendChild(card);
            });
        }
        if (affichageProduits) affichageProduits.textContent = `Affichage de ${liste ? liste.length : 0} produit(s)`;
    }

    // Fonctions panier/profil supprimées

    // Recherche
    const btnRecherche = document.getElementById('btnRecherche');
    const rechercheInput = document.getElementById('rechercheInput');
    if (btnRecherche) btnRecherche.addEventListener('click', () => {
        const q = (rechercheInput && rechercheInput.value || '').toLowerCase();
        const filtrage = produits.filter(p => (p.nom || '').toLowerCase().includes(q));
        afficherProduits(filtrage);
    });

    // Filtrage catégories dynamiques
    const listeCategories = document.getElementById('listeCategories');
    if (listeCategories) {
        listeCategories.querySelectorAll('li').forEach(li => {
            li.addEventListener('click', function() {
                listeCategories.querySelectorAll('li').forEach(el => el.classList.remove('active-cat'));
                li.classList.add('active-cat');
                const cat = li.getAttribute('data-cat');
                categorieFiltree = cat;
                changerPage(1);
            });
        });
    }

    // Recommandations
    const grilleRecomms = document.getElementById('grilleRecomms');
    let indexRecomms = 0;

    function getRecommandations() {
        return [...produits].sort((a, b) => (b.note || 0) - (a.note || 0)).slice(0, 6);
    }

    function afficherRecomms(liste) {
        if (!grilleRecomms) return;
        grilleRecomms.innerHTML = '';
        if (!liste || liste.length === 0) { grilleRecomms.innerHTML = "<p class='placeholder'>Images de produits temporaires ici</p>"; return; }
        liste.forEach((prod, i) => {
            const card = document.createElement('div');
            card.className = 'card recomm-card';
            card.style.animation = 'fadeInUp .5s ease';
            card.innerHTML = `\n        <img src="${prod.image || 'https://via.placeholder.com/400x300'}" alt="${prod.nom}" loading="lazy">\n        <h4>${prod.nom}</h4>\n        <p>${prod.prix} FCFA</p>\n      `;
            card.addEventListener('mouseenter', () => card.style.transform = 'scale(1.08)');
            card.addEventListener('mouseleave', () => card.style.transform = 'scale(1)');
            grilleRecomms.appendChild(card);
        });
    }

    function sliderRecomms() {
        const recs = getRecommandations();
        if (!recs || recs.length === 0) return;
        indexRecomms = (indexRecomms + 1) % recs.length;
        afficherRecomms([recs[indexRecomms]]);
    }
    setInterval(sliderRecomms, 3500);
    afficherRecomms(getRecommandations());

    // Pagination
    function paginerProduits(liste, page) { const debut = (page - 1) * PRODUITS_PAR_PAGE; const fin = debut + PRODUITS_PAR_PAGE; return liste.slice(debut, fin); }

    function genererPagination(totalProduits) {
        const totalPages = Math.ceil(totalProduits / PRODUITS_PAR_PAGE);
        if (!pagination) return;
        pagination.innerHTML = '';
        if (totalPages <= 1) return;
        if (pageActuelle > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'btn-page';
            prevBtn.innerHTML = '&laquo;';
            prevBtn.addEventListener('click', () => changerPage(pageActuelle - 1));
            pagination.appendChild(prevBtn);
        }
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= pageActuelle - 2 && i <= pageActuelle + 2)) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `btn-page ${i===pageActuelle? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', () => changerPage(i));
                pagination.appendChild(pageBtn);
            } else if ((i === pageActuelle - 3 && pageActuelle > 4) || (i === pageActuelle + 3 && pageActuelle < totalPages - 3)) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                pagination.appendChild(dots);
            }
        }
        if (pageActuelle < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.className = 'btn-page';
            nextBtn.innerHTML = '&raquo;';
            nextBtn.addEventListener('click', () => changerPage(pageActuelle + 1));
            pagination.appendChild(nextBtn);
        }
    }

    function changerPage(nouvellePage) {
        pageActuelle = nouvellePage;
        const produitsFilters = filtrerParCategorie(produits, categorieFiltree);
        const produitsTries = trierProduits(produitsFilters, triActuel);
        const produitsPagines = paginerProduits(produitsTries, pageActuelle);
        afficherProduits(produitsPagines);
        genererPagination(produitsFilters.length);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Initialisation simple (vide) - les données réelles doivent être chargées côté serveur via PHP ou API
    afficherProduits(produits);
    afficherRecomms([]);
    genererPagination(0);

    // Export minimal
    window.validerCommande = function() { alert('Fonction de validation à implémenter'); };

});


// === Sélection des éléments ===
const chatbot = document.getElementById("chatbot");
const chatWindow = document.getElementById("chatWindow");
const chatBody = document.getElementById("chatBody");
const userInput = document.getElementById("userInput");
const sendBtn = document.getElementById("sendBtn");

// === Ouvrir / Fermer le chatbot ===
chatbot.onclick = () => {
    chatWindow.style.display = chatWindow.style.display === "block" ? "none" : "block";
};

// === Fonction d’ajout de message ===
function addMessage(content, sender = "bot") {
    const msg = document.createElement("div");
    msg.className = "message" + (sender === "user" ? " user-msg" : "");
    msg.textContent = content;
    chatBody.appendChild(msg);
    chatBody.scrollTop = chatBody.scrollHeight;
}



// === Envoi d’un message utilisateur ===
function sendMessage() {
    const text = userInput.value.trim();
    if (text === "") return;

    addMessage(text, "user");
    userInput.value = "";

    // Réponse automatique après un petit délai
    setTimeout(() => botReply(text), 500);
}

// === Événement clic sur le bouton ===
sendBtn.onclick = sendMessage;

// === Envoi avec la touche "Entrée" ===
userInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") sendMessage();
});
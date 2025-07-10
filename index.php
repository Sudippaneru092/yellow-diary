<?php 
include "admin/db.php";
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get approved businesses with limit
$featuredQuery = "SELECT * FROM businesses WHERE status = 'approved' ORDER BY RAND() LIMIT 12";
$featuredResult = mysqli_query($conn, $featuredQuery);

// Get distinct categories for businesses
$categoriesQuery = "SELECT DISTINCT category FROM businesses WHERE status = 'approved'";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

// Get distinct locations for businesses
$locationsQuery = "SELECT DISTINCT location FROM businesses WHERE status = 'approved'";
$locationsResult = mysqli_query($conn, $locationsQuery);

// Get distinct categories for posts
$postCategoriesQuery = "SELECT DISTINCT category FROM posts WHERE status = 'approved'";
$postCategoriesResult = mysqli_query($conn, $postCategoriesQuery);

// Get approved business posts
$postsQuery = "SELECT p.*, b.name as business_name 
              FROM posts p
              JOIN businesses b ON p.business_id = b.id
              WHERE p.status = 'approved'
              ORDER BY p.created_at DESC
              LIMIT 6";
$postsResult = mysqli_query($conn, $postsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Yellow Diary - Local Business Directory</title>
  
  <link rel="stylesheet" href="index.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <style>
    /* ===== Variables & Reset ===== */
:root {
  --primary-color: #ffc107;
  --primary-dark: #e6ac00;
  --secondary-color: #007bff;
  --dark-bg: #222;
  --light-bg: #fafafa;
  --text-dark: #222;
  --text-light: #f0f0f0;
  --text-muted: #6c757d;
  --shadow-sm: 0 2px 6px rgba(0,0,0,0.08);
  --shadow-md: 0 5px 20px rgba(0,0,0,0.1);
  --shadow-lg: 0 12px 30px rgba(0,0,0,0.2);
  --transition: all 0.3s ease;
  --border-radius: 8px;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
  font-size: 16px;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: var(--light-bg);
  color: var(--text-dark);
  line-height: 1.6;
  padding-bottom: 60px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

@media (prefers-color-scheme: dark) {
  body {
    background: #121212;
    color: #f5f5f5;
  }
}

/* ===== Typography ===== */
:where(h1, h2, h3, h4) {
  line-height: 1.2;
  font-weight: 700;
}

h1 { font-size: 2.5rem; }
h2 { font-size: 2rem; margin-bottom: 1.5rem; }
h3 { font-size: 1.5rem; }

p {
  margin-bottom: 1rem;
}

/* ===== Layout & Containers ===== */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.25rem;
}

.main-content {
  padding: 2rem 0;
}

/* ===== Header & Navigation ===== */
.header {
  background: var(--dark-bg);
  color: var(--text-light);
  padding: 1rem 0;
  box-shadow: 0 3px 15px rgba(0,0,0,0.3);
  position: sticky;
  top: 0;
  z-index: 1000;
  animation: slideDown 0.6s ease forwards;
}

.navbar {
  color: var(--text-light);
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.25rem;
}

.nav {
  display: flex;
  gap: 1.5rem;
}

.nav a {
  color: var(--text-light);
  font-weight: 600;
  position: relative;
  transition: var(--transition);
  text-decoration: none;
}

.nav a:hover {
  color: var(--primary-color);
}

.nav a:focus {
  outline: 2px solid var(--primary-color);
  outline-offset: 4px;
}

.nav a::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  background: var(--primary-color);
  left: 0;
  bottom: -6px;
  transition: var(--transition);
}

.nav a:hover::after {
  width: 100%;
}

/* ===== Hero Section ===== */
.hero {
  background: url('images/hero.jpg') center/cover no-repeat fixed;
  color: var(--text-light);
  padding: 6rem 1.25rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.55);
  z-index: 0;
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 800px;
  margin: 0 auto;
  border-radius: var(--border-radius);
  padding: 3rem 2.5rem;
  background: rgba(0,0,0,0.4);
  animation: fadeInUp 1s ease forwards;
}

/* ===== Search Section ===== */
.search-container {
  background: #ffffff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  margin: -3rem auto 3rem;
  position: relative;
  z-index: 10;
  max-width: 1000px;
}

.search-container h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: var(--dark-bg);
  font-size: 1.8rem;
}

.search-form {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
  align-items: center;
}

.search-form input,
.search-form select {
  flex: 1 1 300px;
  padding: 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: var(--transition);
  background: #f9f9f9;
}

.search-form input:focus,
.search-form select:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25);
  background: #fff;
}

.search-form button {
  padding: 1rem 2rem;
  background: var(--primary-color);
  border: none;
  color: var(--text-dark);
  font-weight: 700;
  cursor: pointer;
  border-radius: 8px;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.search-form button:hover {
  background: var(--primary-dark);
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(230, 172, 0, 0.3);
}
/* ===== Advanced Search ===== */
#advancedSearchForm {
  display: none; /* Hide by default */
}

#advancedSearchForm.show {
  display: flex !important;
  flex-wrap: nowrap !important;
  gap: 1rem !important;
  align-items: center !important;
  overflow-x: auto;
  padding: 0.5rem 0;
  scrollbar-width: thin;
  scrollbar-color: var(--primary-color) transparent;
}

#advancedSearchForm.show::-webkit-scrollbar {
  height: 6px;
}

#advancedSearchForm.show::-webkit-scrollbar-thumb {
  background-color: var(--primary-color);
  border-radius: 3px;
}

#advancedSearchForm.show > * {
  flex: 0 0 auto;
  min-width: 150px;
}

#advancedSearchForm.show > input,
#advancedSearchForm.show > select {
  padding: 0.6rem 0.8rem;
  font-size: 1rem;
  border: 1.5px solid #ddd;
  border-radius: var(--border-radius);
  background: #fafafa;
  transition: border-color 0.3s ease;
}

#advancedSearchForm.show > input:focus,
#advancedSearchForm.show > select:focus {
  border-color: var(--primary-color);
  outline: none;
  background: #fff;
  box-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
}

#advancedSearchForm.show > button {
  cursor: pointer;
  background: var(--primary-color);
  border: none;
  color: var(--text-dark);
  padding: 0.7rem 1.6rem;
  font-weight: 700;
  font-size: 1rem;
  border-radius: var(--border-radius);
  transition: background-color 0.3s ease;
  white-space: nowrap;
}

#advancedSearchForm.show > button:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(230, 172, 0, 0.5);
}

/* Responsive fix for smaller screens */
@media (max-width: 576px) {
  #advancedSearchForm.show {
    padding: 0.5rem 0;
    overflow-x: auto;
  }

  #advancedSearchForm.show > * {
    min-width: 140px;
  }
}

/* ===== Categories ===== */
.categories {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.category {
  padding: 0.75rem 1.5rem;
  background: #e0e0e0;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow-sm);
  border: none;
}

.category:hover {
  background: #ffd54f;
  color: #333;
  transform: scale(1.05);
}

.category.active {
  background: var(--primary-color);
  color: var(--text-dark);
  box-shadow: 0 6px 15px rgba(255, 193, 7, 0.8);
  transform: scale(1.1);
}

/* ===== Featured Businesses Section ===== */
.featured-business {
  padding: 4rem 0;
  background: linear-gradient(to bottom, #f9f9f9 0%, #ffffff 100%);
  text-align: center; /* Center text (including the h2 and button) */
}

.featured-business h2 {
  text-align: center;
  font-size: 2.2rem;
  margin-bottom: 2.5rem;
  color: var(--dark-bg);
  position: relative;
}

.featured-business h2::after {
  content: '';
  display: block;
  width: 80px;
  height: 4px;
  background: var(--primary-color);
  margin: 1rem auto 0;
  border-radius: 2px;
}

.features-grid {
  margin: 0 auto;
  max-width: 1200px;
  justify-content: center;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
}

.feature-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(0, 0, 0, 0.05);
  will-change: transform;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
  border-color: rgba(255, 193, 7, 0.3);
}

.card-image {
  height: 220px;
  overflow: hidden;
  position: relative;
  aspect-ratio: 16/9;
}

.card-image::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
  z-index: 1;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.7s ease;
}

.feature-card:hover .card-image img {
  transform: scale(1.1);
}

.card-content {
  padding: 1.75rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.card-content h3 {
  font-size: 1.4rem;
  margin-bottom: 0.75rem;
  color: var(--dark-bg);
  position: relative;
  padding-bottom: 0.5rem;
}

.card-content h3::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 2px;
  background: var(--primary-color);
}

.card-content p {
  margin: 0.5rem 0;
  color: var(--text-muted);
  font-size: 0.95rem;
}

.card-content p strong {
  color: #444;
  font-weight: 600;
}

.description {
  margin: 1rem 0 !important;
  line-height: 1.6;
  flex-grow: 1;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  position: relative;
}

.description::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 20px;
  background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, white 100%);
}

.card-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.card-actions .btn {
  padding: 0.75rem 1.25rem;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  flex-grow: 1;
  justify-content: center;
  text-align: center;
}

.card-actions .btn:first-child {
  background-color: var(--secondary-color);
  color: white;
  box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
}

.card-actions .btn:last-child {
  background-color: #28a745;
  color: white;
  box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
}

.card-actions .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.category-tag {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(255, 255, 255, 0.9);
  padding: 0.35rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--text-dark);
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  z-index: 2;
  backdrop-filter: blur(5px);
}

.no-results {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 12px;
  box-shadow: var(--shadow-sm);
  color: var(--text-muted);
  font-size: 1.1rem;
  grid-column: 1 / -1;
}

/* ===== Business Posts Section ===== */
.business-posts {
  background: #f5f7fa;
  padding: 4rem 1rem;
}

.business-posts h2 {
  text-align: center;
  font-size: 2.2rem;
  margin-bottom: 2.5rem;
  color: var(--dark-bg);
  position: relative;
}

.business-posts h2::after {
  content: '';
  display: block;
  width: 80px;
  height: 4px;
  background: var(--primary-color);
  margin: 1rem auto 0;
  border-radius: 2px;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  padding: 0 1rem;
}

.post-card {
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  display: flex;
  flex-direction: column;
  position: relative;
  border: 1px solid #ececec;
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.5s ease forwards;
}

.post-card:nth-child(even) {
  animation-delay: 0.1s;
}

.post-card:nth-child(odd) {
  animation-delay: 0.2s;
}

.post-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: rgba(255, 193, 7, 0.3);
}

.post-image {
  position: relative;
  height: 220px;
  overflow: hidden;
  aspect-ratio: 16/9;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.post-card:hover .post-image img {
  transform: scale(1.05);
}

.post-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--primary-color);
  color: var(--text-dark);
  padding: 0.3rem 0.9rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  box-shadow: var(--shadow-sm);
  z-index: 2;
}

.post-content {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.post-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  font-size: 0.85rem;
  color: var(--text-muted);
  flex-wrap: wrap;
  gap: 0.5rem;
}

.post-business {
  font-weight: 600;
  color: var(--secondary-color);
}

.post-date {
  color: #888;
}

.post-content h3 {
  margin-bottom: 0.75rem;
  font-size: 1.3rem;
  color: var(--dark-bg);
  line-height: 1.4;
}

.post-excerpt {
  color: #555;
  margin-bottom: 1rem;
  line-height: 1.6;
  flex-grow: 1;
}

.post-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  color: var(--text-muted);
  font-size: 0.9rem;
}

.post-location i {
  color: var(--secondary-color);
}

.read-more {
  display: inline-block;
  color: var(--primary-color);
  font-weight: 600;
  transition: var(--transition);
  text-decoration: none;
  margin-top: 1rem;
}

.read-more:hover {
  color: var(--primary-dark);
  text-decoration: underline;
}

.no-posts {
  text-align: center;
  grid-column: 1 / -1;
  padding: 2.5rem;
  color: var(--text-muted);
}
.view-all-container {
  margin-top: 2rem;
  display: flex;
  justify-content: center; /* Center the View All button */
}

.view-all-btn {
  padding: 0.75rem 2rem;
  background: var(--primary-color);
  color: var(--text-dark);
  border-radius: 8px;
  font-weight: 700;
  text-decoration: none;
  transition: background-color 0.3s ease;
  box-shadow: 0 5px 15px rgba(255, 193, 7, 0.5);
}

.view-all-btn:hover {
  background: var(--primary-dark);
}

/* ===== Pagination ===== */
.pagination {
  display: flex;
  justify-content: center;
  gap: 0.75rem;
  margin: 3rem 0;
  flex-wrap: wrap;
}

.pagination button {
  padding: 0.6rem 1.2rem;
  border-radius: 30px;
  border: none;
  background: var(--primary-color);
  color: var(--text-dark);
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(255, 193, 7, 0.3);
  transition: var(--transition);
}

.pagination button:hover:not(:disabled) {
  background: var(--primary-dark);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(230, 172, 0, 0.5);
}

.pagination button:disabled {
  cursor: default;
  background: #ccc;
  color: #666;
  box-shadow: none;
}

/* ===== Footer ===== */
.footer {
  background: var(--dark-bg);
  color: var(--text-light);
  text-align: center;
  padding: 2rem 1rem;
  font-size: 0.9rem;
  
  bottom: 0;
  width: 100%;
  z-index: 1000;
  box-shadow: 0 -3px 15px rgba(0, 0, 0, 0.3);
}

.footer a {
  color: var(--primary-color);
  text-decoration: none;
}

.footer a:hover,
.footer a:focus {
  color: var(--primary-dark);
  text-decoration: underline;
  outline: none;
}

/* ===== Accessibility ===== */
.visually-hidden {
  position: absolute !important;
  height: 1px; width: 1px;
  overflow: hidden;
  clip: rect(1px,1px,1px,1px);
  white-space: nowrap;
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
  .navbar {
    flex-wrap: wrap;
    justify-content: center;
  }

  .nav {
    gap: 1rem;
  }

  .search-form {
    flex-direction: column;
    gap: 1rem;
  }

  .categories {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  html {
    font-size: 14px;
  }

  .hero {
    padding: 4rem 1rem;
  }

  .hero-content {
    padding: 2rem 1.25rem;
  }

  .search-container {
    padding: 1.5rem 1rem;
    margin: -2rem auto 2rem;
  }

  .features-grid,
  .posts-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .card-content h3,
  .post-content h3 {
    font-size: 1.2rem;
  }
}

/* ===== Animations ===== */
@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideDown {
  0% {
    transform: translateY(-100%);
  }
  100% {
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  0% { opacity: 0; }
  100% { opacity: 1; }
}


    .category,
.post-category {
  
  display: inline-block;
  margin: 0.25rem;
  text-decoration: none;
  color: var(--text-dark);
  font-size: 0.875rem;
  padding: 0.75rem 1.5rem;
  background: #e0e0e0;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow-sm);
  border: none;
  
}

.category:hover,
.post-category:hover {
  background: #ffd54f;
  color: #333;
  transform: scale(1.05);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  font-weight: 700;
  transition: all 0.3s ease-in-out;
  text-decoration: none;
  color: var(--text-dark);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  font-size: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: inline-block;
  margin: 0.25rem;
  text-align: center;
  color: var(--text-dark);
  transition: all 0.3s ease;
  text-decoration: none;
  
}

.category.active,
.post-category.active {
  background: var(--primary-color);
  color: var(--text-dark);
  box-shadow: 0 6px 15px rgba(255, 193, 7, 0.8);
  transform: scale(1.1);
  font-weight: 700;

}
.advanced-toggle button {
  margin-top: 1rem;
  background: var(--primary-color);
  color: var(--text-dark);
  font-weight: 700;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  border: none;
  border-radius: var(--border-radius);
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  justify-content: center;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  text-decoration: none;
  
}

.advanced-toggle button:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(230, 172, 0, 0.4);
}

  
  </style>

</head>
<body>

<?php include "header.php"; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1>Welcome to Yellow Diary</h1>
    <p>Your go-to directory for local businesses in Nepal.</p>
  </div>
</section>

<main class="main-content">
  <!-- Search Section -->
  <section class="search-container">
    <h2>🔍 Find a Business</h2>
    <form action="search_results.php" method="GET" class="search-form basic-search">
      <input type="text" name="query" id="searchInput" placeholder="Search businesses, categories, locations..." required
             list="searchSuggestions" autocomplete="off">
      <datalist id="searchSuggestions">
        <?php 
        // Combine business names, categories, and locations for suggestions
        $suggestionsQuery = "(SELECT name as suggestion FROM businesses WHERE status='approved' LIMIT 15)
                            UNION
                            (SELECT category as suggestion FROM businesses WHERE status='approved' LIMIT 15)
                            UNION
                            (SELECT location as suggestion FROM businesses WHERE status='approved' LIMIT 15)";
        $suggestionsResult = mysqli_query($conn, $suggestionsQuery);
        while($suggestion = mysqli_fetch_assoc($suggestionsResult)):
        ?>
          <option value="<?= htmlspecialchars($suggestion['suggestion']) ?>">
        <?php endwhile; ?>
      </datalist>
      <button type="submit">Search</button>
    </form>

    <div class="advanced-toggle">
      <button type="button" onclick="toggleAdvancedSearch()">⚙️ Advanced Search</button>
    </div>

    <form id="advancedSearchForm" action="search_results.php" method="GET" class="search-form advanced-search">
      <div class="form-row">
        <div class="form-group">
          <input type="text" name="name" id="nameInput" placeholder="Business name">
        </div>
        
        <div class="form-group">
          <input type="text" name="category" id="categoryInput" placeholder="Category (e.g. 'Edu')" 
                 list="categorySuggestions" autocomplete="off">
          <datalist id="categorySuggestions">
            <?php mysqli_data_seek($categoriesResult, 0); while($cat = mysqli_fetch_assoc($categoriesResult)): ?>
              <option value="<?= htmlspecialchars($cat['category']) ?>">
            <?php endwhile; ?>
          </datalist>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <input type="text" name="location" id="locationInput" placeholder="Location" 
                 list="locationSuggestions" autocomplete="off">
          <datalist id="locationSuggestions">
            <?php mysqli_data_seek($locationsResult, 0); while($loc = mysqli_fetch_assoc($locationsResult)): ?>
              <option value="<?= htmlspecialchars($loc['location']) ?>">
            <?php endwhile; ?>
          </datalist>
          <button type="button" id="useCurrentLocationBtn" class="location-btn">📍 Use My Location</button>
        </div>
        
        <div class="form-group">
          <select name="radius">
            <option value="">Any distance</option>
            <option value="1">Within 1 km</option>
            <option value="3">Within 3 km</option>
            <option value="5">Within 5 km</option>
            <option value="10">Within 10 km</option>
          </select>
        </div>
      </div>
      
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">
      <button type="submit" class="search-button">Advanced Search</button>
    </form>
  </section>

  <!-- Categories -->
  <div class="categories">
    <button class="category active" data-category="all">All</button>
    <?php mysqli_data_seek($categoriesResult, 0); while ($cat = mysqli_fetch_assoc($categoriesResult)): ?>
      <button class="category" data-category="<?= strtolower($cat['category']) ?>"><?= htmlspecialchars($cat['category']) ?></button>
    <?php endwhile; ?>
  </div>

  <!-- Featured Businesses -->
  <section class="features-business">
    <h2>Featured Businesses</h2>
    <?php if(mysqli_num_rows($featuredResult) > 0): ?>
    <div class="features-grid">
      <?php while ($row = mysqli_fetch_assoc($featuredResult)):
        $imagePath = (!empty($row['image'])) ? "admin/".$row['image'] : 'images/default-business.png';
        $categoryClass = strtolower($row['category']);
      ?>
        <article class="feature-card" data-category="<?= $categoryClass ?>">
          <div class="card-image">
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>" onerror="this.src='images/default-business.png'">
          <?php echo $imagePath;?>
          </div>
          <div class="card-content">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><strong>Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
            <p class="description"><?= htmlspecialchars($row['description']) ?></p>
            <div class="card-actions">
              <?php if(!empty($row['url'])): ?>
              <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" class="btn">Visit Website</a>
              <?php endif; ?>
              <?php if(!empty($row['phone'])): ?>
              <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="btn">Call Now</a>
              <?php endif; ?>
            </div>
          </div>
        </article>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
      <p>No featured businesses found.</p>
    <?php endif; ?>
  </section>

  <!-- Business Posts -->
  <section class="business-posts">
    <div class="container">
      <h2>Business News & Updates</h2>

      <div class="post-categories">
        <button class="post-category active" data-category="all">All</button>
        <?php mysqli_data_seek($postCategoriesResult, 0); while ($postCat = mysqli_fetch_assoc($postCategoriesResult)):
          $postCatVal = strtolower($postCat['category']);
        ?>
          <button class="post-category" data-category="<?= $postCatVal ?>"><?= htmlspecialchars($postCat['category']) ?></button>
        <?php endwhile; ?>
      </div>

      <div class="posts-grid">
        <?php if(mysqli_num_rows($postsResult) > 0): ?>
          <?php while($post = mysqli_fetch_assoc($postsResult)):
            $postImage = !empty($post['image']) ? 'admin/uploads/posts/' . $post['image'] : 'images/default-post.png';
            $postCategoryClass = strtolower($post['category']);
          ?>
            <article class="post-card" data-category="<?= $postCategoryClass ?>">
              <div class="post-image">
                <img src="<?= $postImage ?>" alt="<?= htmlspecialchars($post['title']) ?>" onerror="this.src='images/default-post.png'">
                <div class="post-badge"><?= htmlspecialchars($post['category']) ?></div>
              </div>
              <div class="post-content">
                <div class="post-meta">
                  <span class="post-business"><?= htmlspecialchars($post['business_name']) ?></span>
                  <span class="post-date"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                </div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p class="post-excerpt"><?= htmlspecialchars(substr($post['description'], 0, 150)) ?>...</p>
                <div class="post-location">
                  <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($post['location']) ?>
                </div>
                <a href="post.php?id=<?= $post['id'] ?>" class="read-more">Read More</a>
              </div>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No business updates available yet.</p>
        <?php endif; ?>
      </div>

      <div class="view-all-container">
        <a href="posts.php" class="view-all-btn">View All Updates</a>
      </div>
    </div>
  </section>
</main>

<footer class="footer">
  <p>&copy; <?= date('Y') ?> Yellow Diary. All rights reserved.</p>
  <p>
    <a href="privacy.php">Privacy Policy</a> | 
    <a href="terms.php">Terms of Service</a> |
    <a href="admin_login.php">Admin Login</a>
  </p>
</footer>

<script>
// Geolocation for advanced search
if (navigator.geolocation) {
  document.getElementById('useCurrentLocationBtn').addEventListener('click', function() {
    navigator.geolocation.getCurrentPosition(function(position) {
      document.getElementById('lat').value = position.coords.latitude;
      document.getElementById('lng').value = position.coords.longitude;
      document.getElementById('locationInput').value = "Near Me";
    }, function(error) {
      alert("Could not get your location. Please enable location services.");
    });
  });
}

// Toggle advanced search
function toggleAdvancedSearch() {
  const advForm = document.getElementById("advancedSearchForm");
  advForm.classList.toggle("show");
  const btn = document.querySelector('.advanced-toggle button');
  if (advForm.classList.contains('show')) {
    btn.textContent = '⚙️ Hide Advanced';
  } else {
    btn.textContent = '⚙️ Advanced Search';
  }
}

// Live search suggestions
document.getElementById('searchInput').addEventListener('input', function() {
  const input = this.value.trim();
  if (input.length > 1) {
    // In a real implementation, you would make an AJAX call here
    // to get dynamic suggestions based on the input
    console.log("Fetching suggestions for:", input);
  }
});

// Filter businesses by category
document.querySelector('.categories').addEventListener('click', (e) => {
  if (e.target.classList.contains('category')) {
    const category = e.target.getAttribute('data-category');
    filterBusinesses(category);
    document.querySelectorAll('.categories .category').forEach(btn => {
      btn.classList.remove('active');
    });
    e.target.classList.add('active');
  }
});

function filterBusinesses(category) {
  document.querySelectorAll('.feature-card').forEach(card => {
    const cardCategory = card.getAttribute('data-category');
    card.style.display = (category === "all" || cardCategory === category) ? "flex" : "none";
  });
}

// Filter posts by category
document.querySelector('.post-categories').addEventListener('click', (e) => {
  if (e.target.classList.contains('post-category')) {
    const category = e.target.getAttribute('data-category');
    filterPosts(category);
    document.querySelectorAll('.post-categories .post-category').forEach(btn => {
      btn.classList.remove('active');
    });
    e.target.classList.add('active');
  }
});

function filterPosts(category) {
  document.querySelectorAll('.post-card').forEach(post => {
    const postCategory = post.getAttribute('data-category');
    post.style.display = (category === "all" || postCategory === category) ? "flex" : "none";
  });
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', () => {
  filterBusinesses("all");
  filterPosts("all");
});
</script>
</body>
</html>
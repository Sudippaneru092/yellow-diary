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
  <meta name="description" content="Yellow Diary - Your go-to directory for local businesses in Nepal">
  <title>Yellow Diary - Local Business Directory</title>
 <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Font Awesome for icons -->
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

.advanced-toggle {
  text-align: center;
  margin: 1rem 0;
  width: 100%;
}

.advanced-toggle button {
  background: none;
  border: none;
  color: var(--secondary-color);
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  padding: 0.75rem;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.advanced-toggle button:hover {
  color: #0056b3;
}

.advanced-search {
  display: none;
  width: 100%;
  gap: 1rem;
  animation: fadeIn 0.3s ease-out;
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
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  padding: 0 1rem;
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

/* ===== Business Posts Section (Updated) ===== */
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
  font-size: 1.1rem;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
}
.post-card {
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


/* ===== Single Post Styles ===== */
.single-post .container {
  max-width: 900px;
}

.breadcrumb {
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  color: var(--text-muted);
}

.breadcrumb a {
  color: var(--secondary-color);
  text-decoration: none;
}

.post-full {
  background: #fff;
  padding: 2rem;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
}

.post-header {
  margin-bottom: 2rem;
  text-align: center;
}

.post-header h1 {
  font-size: 2rem;
  margin: 1rem 0;
}

.post-meta {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  color: var(--text-muted);
}

.post-business {
  font-weight: 600;
  color: var(--secondary-color);
}

.post-category-location {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
}

.post-image-full {
  margin: 2rem 0;
}

.post-image-full img {
  width: 100%;
  max-height: 500px;
  object-fit: cover;
  border-radius: var(--border-radius);
}

.post-content-full {
  line-height: 1.8;
  font-size: 1.1rem;
}

.post-content-full p {
  margin-bottom: 1.5rem;
}

.business-contact {
  margin: 3rem 0;
  padding: 2rem;
  background: #f8f9fa;
  border-radius: var(--border-radius);
}

.business-contact h3 {
  margin-bottom: 1.5rem;
  color: var(--secondary-color);
}

.contact-details p {
  margin-bottom: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.contact-details a {
  color: var(--text-dark);
  transition: var(--transition);
  text-decoration: none;
}

.contact-details a:hover {
  color: var(--secondary-color);
}

/* Related Posts */
.related-posts {
  margin-top: 4rem;
}

.related-posts h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  color: var(--secondary-color);
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.related-card {
  transition: var(--transition);
}

.related-card:hover {
  transform: translateY(-5px);
}

.related-image {
  height: 150px;
  overflow: hidden;
  border-radius: var(--border-radius);
  margin-bottom: 0.5rem;
  aspect-ratio: 16/9;
}

.related-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition);
}

.related-card:hover .related-image img {
  transform: scale(1.05);
}

.related-card h3 {
  font-size: 1.1rem;
  margin-bottom: 0.3rem;
}

.related-date {
  font-size: 0.8rem;
  color: var(--text-muted);
}
/* ===== Post Categories Filter Buttons ===== */
.post-categories {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.75rem;
  margin: 1.5rem 0;
}

.post-category {
  padding: 0.65rem 1.25rem;
  background: #e0e0e0;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  border: none;
  box-shadow: var(--shadow-sm);
}

.post-category:hover {
  background: #ffd54f;
  color: #333;
  transform: scale(1.05);
}

.post-category.active {
  background: var(--primary-color);
  color: var(--text-dark);
  box-shadow: 0 6px 15px rgba(255, 193, 7, 0.7);
  transform: scale(1.1);
}

/* Fix advanced search toggle reveal */
#advancedSearchForm.show {
  display: flex !important;
  flex-wrap: wrap;
}

/* Improve small screen readability for post content */
@media (max-width: 576px) {
  .post-content h3 {
    font-size: 1.1rem;
  }

  .post-meta {
    flex-direction: column;
    gap: 0.25rem;
    align-items: flex-start;
  }

  .post-category {
    padding: 0.4rem 0.75rem;
    font-size: 0.85rem;
  }

  .read-more {
    display: block;
    margin-top: 0.75rem;
  }
}

/* Utility for ARIA visibility toggling */
[aria-hidden="true"] {
  display: none !important;
}


/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin: 3rem 0;
  flex-wrap: wrap;
}

.page-link {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: #f0f0f0;
  color: var(--text-dark);
  border-radius: 4px;
  transition: var(--transition);
  text-decoration: none;
  border: none;
  cursor: pointer;
}

.page-link:hover, .page-link.active {
  background: var(--primary-color);
  color: var(--text-dark);
}

/* ===== Footer ===== */
.footer {
  background: var(--dark-bg);
  color: var(--text-light);
  padding: 1.25rem;
  text-align: center;
  position: fixed;
  bottom: 0;
  width: 100%;
}

.footer p {
  margin-bottom: 0.5rem;
  color: #ccc;
}

.footer a {
  color: var(--primary-color);
  font-weight: 600;
  transition: var(--transition);
  text-decoration: none;
}

.footer a:hover {
  color: var(--primary-dark);
}

/* ===== Animations ===== */
@keyframes slideDown {
  from { transform: translateY(-100%); }
  to { transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeInUp {
  from { 
    opacity: 0; 
    transform: translateY(20px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

/* ===== Responsive Design ===== */
@media (max-width: 992px) {
  html { font-size: 15px; }
  
  .hero-content h1 {
    font-size: 2.2rem;
  }
  
  .features-grid,
  .posts-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}

@media (max-width: 768px) {
  .header {
    padding: 0.5rem 0;
  }
  
  .navbar {
    flex-direction: column;
    gap: 1rem;
  }
  
  .nav {
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
  }
  
  .hero {
    padding: 4rem 1rem;
  }
  
  .hero-content {
    padding: 2rem 1rem;
  }
  
  .search-container {
    margin: -2rem auto 2rem;
    padding: 1.5rem;
  }
  
  .features-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}

@media (max-width: 576px) {
  html { font-size: 14px; }
  
  .hero-content h1 {
    font-size: 1.8rem;
  }
  
  .category {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }
  
  .features-grid,
  .posts-grid {
    grid-template-columns: 1fr;
  }
  
  .post-image {
    height: 160px;
  }
  
  .card-actions {
    flex-direction: column;
  }
  
  .search-form button {
    width: 100%;
  }
}

@media (max-width: 400px) {
  .nav {
    gap: 0.75rem;
  }
  
  .hero-content {
    padding: 1.5rem 0.75rem;
  }
  
  .hero-content h1 {
    font-size: 1.5rem;
  }
  
  .hero-content p {
    font-size: 1rem;
  }
}
  </style>

<!-- Header -->
<header class="header">
  <div class="navbar">
    <h1>Yellow Diary</h1>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="service.php">Services</a>
      <a href="submit_business.php">Submit Business</a>
      <?php if(isset($_SESSION['business_id'])): ?>
        <a href="submit_post.php">Submit Post</a>
      <?php endif; ?>
      <a href="contact.php">Contact</a>
      <?php if(isset($_SESSION['business_id'])): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="business_login.php">Business Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Hero Section -->
<section class="hero" aria-label="Welcome to Yellow Diary">
  <div class="hero-content">
    <h1>Welcome to Yellow Diary</h1>
    <p>Your go-to directory for local businesses in Nepal.</p>
  </div>
</section>

<!-- Search Section -->
<main class="main-content">
  <section class="search-container" aria-label="Business search">
    <h2>🔍 Find a Business</h2>

    <!-- Basic Search -->
    <form action="search.php" method="GET" class="search-form basic-search">
      <input type="text" name="query" placeholder="Search businesses..." required
             aria-label="Search businesses">
      <button type="submit">Search</button>
    </form>

    <!-- Advanced Search Toggle -->
    <div class="advanced-toggle">
      <button type="button" onclick="tog()" aria-expanded="false" aria-controls="advancedSearchForm">
        ⚙️ Advanced Search
      </button>
    </div>

    <!-- Advanced Search Form -->
    <form id="advancedSearchForm" action="advanced_search.php" method="GET" class="search-form advanced-search">
      <input type="text" name="location" placeholder="City or Area" aria-label="Location">
      
      <select name="category" aria-label="Business category">
        <option value="">All Categories</option>
        <?php while($cat = mysqli_fetch_assoc($categoriesResult)): ?>
          <option value="<?= htmlspecialchars($cat['category']) ?>">
            <?= htmlspecialchars($cat['category']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      
      <select name="radius" aria-label="Search radius">
        <option value="">Nearby (km)</option>
        <option value="1">1 km</option>
        <option value="3">3 km</option>
        <option value="5">5 km</option>
        <option value="10">10 km</option>
        <option value="25">25 km</option>
      </select>
      <!-- Hidden inputs to store browser location -->
      <input type="hidden" name="lat" id="lat" value="">
      <input type="hidden" name="lng" id="lng" value="">

      <button type="submit">Advanced Search</button>
    </form>
  </section>

  <!-- Categories for Businesses -->
  <div class="categories" role="tablist" aria-label="Business categories">
    <button class="category active" data-category="all" role="tab" aria-selected="true">All</button>
    <?php 
    mysqli_data_seek($categoriesResult, 0); // Reset pointer
    while ($cat = mysqli_fetch_assoc($categoriesResult)): 
      $catVal = strtolower($cat['category']);
    ?>
      <button class="category" data-category="<?= $catVal ?>" role="tab" aria-selected="false">
        <?= htmlspecialchars($cat['category']) ?>
      </button>
    <?php endwhile; ?>
  </div>

  <!-- Featured Businesses -->
  <section class="features-business" aria-label="Featured businesses">
    <h2>Featured Businesses</h2>
    
    <?php if(mysqli_num_rows($featuredResult) > 0): ?>
      <div class="features-grid">
        <?php while ($row = mysqli_fetch_assoc($featuredResult)): 
          $imagePath = !empty($row['image']) && file_exists('admin/' . $row['image']) 
                       ? 'admin/' . $row['image'] 
                       : 'images/default.jpg'; // Default image if not found
          
          // If image path is relative, prepend admin/uploads if necessary
          if (strpos($row['image'], 'uploads/') === 0) {
            $imagePath = 'admin/' . $row['image'];
          }
          
          // Ensure image path is valid
          if (!file_exists($imagePath)) {
            $imagePath = 'images/default.jpg'; // Fallback image
          } 
          $categoryClass = strtolower($row['category']);
        ?>
          <article class="feature-card" data-category="<?= $categoryClass ?>">
            <div class="card-image">
              <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy">
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p><strong>Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
              <p class="description"><?= htmlspecialchars($row['description']) ?></p>
              <div class="card-actions">
                <?php if(!empty($row['url'])): ?>
                  <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn">
                    Visit Website
                  </a>
                <?php endif; ?>
                <?php if(!empty($row['phone'])): ?>
                  <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="btn">
                    Call Now
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="no-results">No featured businesses found. Check back later!</p>
    <?php endif; ?>
  </section>

  <!-- Business Posts Section -->
  <section class="business-posts" aria-label="Business news and updates">
    <div class="container">
      <h2>Business News & Updates</h2>
      
      <!-- Post Categories -->
      <div class="post-categories" role="tablist" aria-label="Post categories">
        <button class="post-category active" data-category="all" role="tab" aria-selected="true">All</button>
        <?php 
        mysqli_data_seek($postCategoriesResult, 0); // Reset pointer just in case
        while ($postCat = mysqli_fetch_assoc($postCategoriesResult)):
          $postCatVal = strtolower($postCat['category']);
        ?>
          <button class="post-category" data-category="<?= $postCatVal ?>" role="tab" aria-selected="false">
            <?= htmlspecialchars($postCat['category']) ?>
          </button>
        <?php endwhile; ?>
      </div>

      <div class="posts-grid">
        <?php if(mysqli_num_rows($postsResult) > 0): ?>
          <?php while($post = mysqli_fetch_assoc($postsResult)): 
            $postImage = !empty($post['image']) ? 'admin/uploads/posts/'.$post['image'] : 'images/default-post.jpg';
          ?>
            <article class="post-card">
              <div class="post-image">
                <img src="<?= $postImage ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                <div class="post-badge">
                  <?= htmlspecialchars($post['category']) ?>
                </div>
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
          <p class="no-posts">No business updates available yet. Check back soon!</p>
        <?php endif; ?>
      </div>
      
      <div class="view-all-container">
        <a href="posts.php" class="view-all-btn">View All Updates</a>
      </div>
    </div>
  </section>
</main>

<!-- Footer -->
<footer class="footer">
  <p>&copy; <?= date('Y') ?> Yellow Diary. All rights reserved.</p>
  <p>
    <a href="privacy.php">Privacy Policy</a> | 
    <a href="terms.php">Terms of Service</a> |
    <a href="admin_login.php">Admin Login</a>
  </p>
</footer>

<script>
// Get user location and fill hidden inputs
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function(position) {
    document.getElementById("lat").value = position.coords.latitude;
    document.getElementById("lng").value = position.coords.longitude;
  }, function(error) {
    console.warn('Geolocation error:', error.message);
    // User denied or error — lat/lng stay empty, radius search will not apply
  });
} else {
  console.warn("Geolocation is not supported by this browser.");
}
</script>

<!-- JavaScript -->
<script>
  // Toggle advanced search
  function tog() {
    const advForm = document.getElementById("advancedSearchForm");
    const toggleButton = document.querySelector('.advanced-toggle button');
    const isExpanded = advForm.classList.toggle("show");
    toggleButton.setAttribute('aria-expanded', isExpanded);
  }

  // Category filtering for businesses
  document.querySelector('.categories').addEventListener('click', (e) => {
    if (e.target.classList.contains('category')) {
      const category = e.target.getAttribute('data-category');
      filterBusinesses(category);
      
      // Update active state and ARIA attributes
      document.querySelectorAll('.categories .category').forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
      });
      
      e.target.classList.add('active');
      e.target.setAttribute('aria-selected', 'true');
    }
  });

  function filterBusinesses(category) {
    const cards = document.querySelectorAll('.feature-card');
    cards.forEach(card => {
      const cardCategory = card.getAttribute('data-category');
      if (category === "all" || cardCategory === category) {
        card.style.display = "flex";
        card.setAttribute('aria-hidden', 'false');
      } else {
        card.style.display = "none";
        card.setAttribute('aria-hidden', 'true');
      }
    });
  }

  // Post category filtering
  document.querySelector('.post-categories').addEventListener('click', (e) => {
    if (e.target.classList.contains('post-category')) {
      const category = e.target.getAttribute('data-category');
      filterPosts(category);
      
      // Update active state and ARIA attributes
      document.querySelectorAll('.post-categories .post-category').forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
      });
      
      e.target.classList.add('active');
      e.target.setAttribute('aria-selected', 'true');
    }
  });

  function filterPosts(category) {
    const posts = document.querySelectorAll('.post-card');
    posts.forEach(post => {
      const postCategory = post.querySelector('.post-badge').textContent.toLowerCase();
      if (category === "all" || postCategory === category) {
        post.style.display = "flex"; // adjust if your CSS uses block or grid
        post.setAttribute('aria-hidden', 'false');
      } else {
        post.style.display = "none";
        post.setAttribute('aria-hidden', 'true');
      }
    });
  }

  // Initialize filters on DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    filterBusinesses("all");
    filterPosts("all");
  });
</script>

</body>
</html>

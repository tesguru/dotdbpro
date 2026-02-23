import { useState } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import Navbar from '@/Components/landingpage/NavBar';
import Hero from '@/Components/landingpage/Hero';
import Features from '@/Components/landingpage/Features';
import SearchBar from '@/Components/landingpage/SearchBar';
import Stats from '@/Components/landingpage/Stats';
import Footer from '@/Components/landingpage/Footer';
import CTA from '@/Components/landingpage/Cta';

export default function Home({ searchResults }) {
  const [hasSearched, setHasSearched] = useState(false);

  return (
    <MainLayout>
      <div className="relative z-10">
        <Navbar />
        <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-20 pb-12 sm:pb-24">
          {!hasSearched && <Hero />}
          <SearchBar onSearch={() => setHasSearched(true)} searchResults={searchResults} />
          {!hasSearched && <Features />}
          {!hasSearched && <Stats />}
          {!hasSearched && <CTA />}
        </main>
        <Footer />
      </div>
    </MainLayout>
  );
}

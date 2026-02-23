import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import { ThemeProvider } from './context/ThemeContext'
import { ToastProvider } from './context/ToastProvider' // 👈 add this
import '@fontsource/urbanist/400.css'
import '@fontsource/urbanist/500.css'
import '@fontsource/urbanist/600.css'
import '@fontsource/urbanist/700.css'
import '../css/app.css'

document.fonts.ready.then(() => {
  console.log('All fonts loaded:', [...document.fonts].map(f => f.family));
});

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.{jsx,js}', { eager: true })
    return pages[`./Pages/${name}.jsx`]
  },
  setup({ el, App, props }) {
    createRoot(el).render(
      <ThemeProvider>
        <ToastProvider>  {/* 👈 add this */}
          <App {...props} />
        </ToastProvider>  {/* 👈 close this */}
      </ThemeProvider>
    )
  },
  progress: {
    color: '#4B5563',
  },
})

import { useState } from 'react';
import { useTheme } from '@/context/ThemeContext';
import { X } from 'lucide-react';

export default function Footer() {
  const { isDark } = useTheme();
  const [activeModal, setActiveModal] = useState(null);

  const ComingSoonModal = () => (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className={`relative max-w-md w-full rounded-2xl p-6 sm:p-8 transition-colors duration-500 ${
        isDark ? 'bg-gray-900 border border-white/10' : 'bg-white border border-purple-200'
      }`}>
        <button onClick={() => setActiveModal(null)} className={`absolute top-4 right-4 p-2 rounded-lg transition-colors ${isDark ? 'hover:bg-white/10' : 'hover:bg-gray-100'}`}>
          <X className={`w-5 h-5 ${isDark ? 'text-white' : 'text-gray-900'}`} />
        </button>
        <div className="text-center">
          <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
            <span className="text-2xl">🚀</span>
          </div>
          <h3 className={`text-2xl font-bold mb-3 ${isDark ? 'text-white' : 'text-gray-900'}`}>Coming Soon!</h3>
          <p className={`text-base ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
            We're working hard to bring you this feature. Stay tuned for updates!
          </p>
        </div>
      </div>
    </div>
  );

  const TermsModal = () => (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto">
      <div className={`relative max-w-3xl w-full rounded-2xl p-6 sm:p-8 my-8 transition-colors duration-500 ${
        isDark ? 'bg-gray-900 border border-white/10' : 'bg-white border border-purple-200'
      }`}>
        <button onClick={() => setActiveModal(null)} className={`absolute top-4 right-4 p-2 rounded-lg transition-colors ${isDark ? 'hover:bg-white/10' : 'hover:bg-gray-100'}`}>
          <X className={`w-5 h-5 ${isDark ? 'text-white' : 'text-gray-900'}`} />
        </button>
        <div className="max-h-[70vh] overflow-y-auto pr-4">
          <h3 className={`text-3xl font-bold mb-6 ${isDark ? 'text-white' : 'text-gray-900'}`}>Terms & Conditions</h3>
          <div className={`space-y-6 ${isDark ? 'text-purple-100' : 'text-gray-700'}`}>
            {[
              { title: '1. Acceptance of Terms', content: 'By accessing and using Dnwhouse ("the Service"), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our service.' },
              { title: '2. Service Description', content: 'Dnwhouse provides a domain search and keyword research platform that allows users to search across 350M+ domains. We provide tools for domain discovery, keyword analysis, and related search insights.' },
              { title: '6. Intellectual Property', content: 'All content, features, and functionality of Dnwhouse are owned by us and are protected by copyright, trademark, and other intellectual property laws.' },
              { title: '7. Limitation of Liability', content: 'Dnwhouse is provided "as is" without warranties of any kind. We shall not be liable for any indirect, incidental, or consequential damages arising from your use of the service.' },
            ].map((section) => (
              <section key={section.title}>
                <h4 className={`text-lg font-semibold mb-2 ${isDark ? 'text-white' : 'text-gray-900'}`}>{section.title}</h4>
                <p className="text-sm leading-relaxed">{section.content}</p>
              </section>
            ))}
            <p className={`text-xs mt-8 ${isDark ? 'text-purple-300' : 'text-gray-500'}`}>Last updated: December 20, 2025</p>
          </div>
        </div>
      </div>
    </div>
  );

  const PrivacyModal = () => (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto">
      <div className={`relative max-w-3xl w-full rounded-2xl p-6 sm:p-8 my-8 transition-colors duration-500 ${
        isDark ? 'bg-gray-900 border border-white/10' : 'bg-white border border-purple-200'
      }`}>
        <button onClick={() => setActiveModal(null)} className={`absolute top-4 right-4 p-2 rounded-lg transition-colors ${isDark ? 'hover:bg-white/10' : 'hover:bg-gray-100'}`}>
          <X className={`w-5 h-5 ${isDark ? 'text-white' : 'text-gray-900'}`} />
        </button>
        <div className="max-h-[70vh] overflow-y-auto pr-4">
          <h3 className={`text-3xl font-bold mb-6 ${isDark ? 'text-white' : 'text-gray-900'}`}>Privacy Policy</h3>
          <div className={`space-y-6 ${isDark ? 'text-purple-100' : 'text-gray-700'}`}>
            {[
              { title: '1. Information We Collect', content: 'We collect account information (email, name, password), usage data (search queries, domains viewed), technical data (IP address, browser type), and communication data.' },
              { title: '4. Data Security', content: 'We implement industry-standard security measures including encryption, secure servers, and access controls. However, no method of transmission over the internet is 100% secure.' },
              { title: '5. Cookies and Tracking', content: 'We use cookies and similar technologies to enhance your experience, analyze usage, and remember your preferences. You can control cookies through your browser settings.' },
              { title: '8. Children\'s Privacy', content: 'Dnwhouse is not intended for users under 13 years of age. We do not knowingly collect personal information from children under 13.' },
            ].map((section) => (
              <section key={section.title}>
                <h4 className={`text-lg font-semibold mb-2 ${isDark ? 'text-white' : 'text-gray-900'}`}>{section.title}</h4>
                <p className="text-sm leading-relaxed">{section.content}</p>
              </section>
            ))}
            <p className={`text-xs mt-8 ${isDark ? 'text-purple-300' : 'text-gray-500'}`}>Last updated: December 20, 2025</p>
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <>
      <footer className={`border-t backdrop-blur-xl mt-12 sm:mt-20 transition-colors duration-500 ${
        isDark ? 'border-white/10 bg-black/20' : 'border-purple-200 bg-white/60'
      }`}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
          <div className="flex flex-col sm:flex-row items-center justify-between space-y-6 sm:space-y-0 mb-8">
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
                <span className="font-bold text-sm text-white">D</span>
              </div>
              <span className={`text-lg font-semibold transition-colors duration-500 ${isDark ? 'text-white' : 'text-gray-900'}`}>Dnwhouse</span>
            </div>

            <div className="flex flex-wrap items-center justify-center gap-6">
              {['Pricing', 'API'].map((item) => (
                <button key={item} onClick={() => setActiveModal('coming-soon')} className={`text-sm transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'}`}>
                  {item}
                </button>
              ))}
              <button onClick={() => setActiveModal('terms')} className={`text-sm transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'}`}>
                Terms & Conditions
              </button>
              <button onClick={() => setActiveModal('privacy')} className={`text-sm transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'}`}>
                Privacy Policy
              </button>
            </div>
          </div>

          <div className={`border-t pt-6 text-center transition-colors duration-500 ${isDark ? 'border-white/10' : 'border-purple-200'}`}>
            <p className={`text-sm transition-colors duration-500 ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
              © {new Date().getFullYear()} Dnwhouse. All rights reserved.
            </p>
          </div>
        </div>
      </footer>

      {activeModal === 'coming-soon' && <ComingSoonModal />}
      {activeModal === 'terms' && <TermsModal />}
      {activeModal === 'privacy' && <PrivacyModal />}
    </>
  );
}

import { useState } from 'react';
import { useTheme } from '@/context/ThemeContext';
import {
  Download,
  ChevronLeft,
  ChevronRight,
  ArrowUpDown,
  ChevronUp,
} from 'lucide-react';
import { AnimatePresence } from 'framer-motion';


export default function DomainTable({ results, onAIAnalysis, onCheckExpiry }) {
  const { isDark } = useTheme();
  const [currentPage, setCurrentPage] = useState(1);
  const [sortBy, setSortBy] = useState('keyword');
  const [sortOrder, setSortOrder] = useState('asc');
  const [selectedKeyword, setSelectedKeyword] = useState(null);
  const [expandedRows, setExpandedRows] = useState(new Set());
  const resultsPerPage = 30;
  const maxVisibleExtensions = 5;

  const getDataArray = () => {
    if (!results) return [];
    if (typeof results === 'object' && 'data' in results) {
      return Array.isArray(results.data) ? results.data : [];
    }
    if (Array.isArray(results)) return results;
    return [];
  };

  const dataArray = getDataArray();

  const sortedResults = [...dataArray].sort((a, b) => {
    let comparison = 0;
    if (sortBy === 'keyword') {
      comparison = a.keyword.localeCompare(b.keyword);
    } else if (sortBy === 'count') {
      comparison = a.count - b.count;
    }
    return sortOrder === 'asc' ? comparison : -comparison;
  });

  const totalPages = Math.ceil(sortedResults.length / resultsPerPage);
  const startIndex = (currentPage - 1) * resultsPerPage;
  const endIndex = startIndex + resultsPerPage;
  const currentResults = sortedResults.slice(startIndex, endIndex);

  const handleSort = (column) => {
    if (sortBy === column) {
      setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
    } else {
      setSortBy(column);
      setSortOrder('asc');
    }
  };

  const toggleRowExpansion = (index) => {
    const newExpandedRows = new Set(expandedRows);
    if (newExpandedRows.has(index)) {
      newExpandedRows.delete(index);
    } else {
      newExpandedRows.add(index);
    }
    setExpandedRows(newExpandedRows);
  };

  const handleExport = () => {
    const csv = [
      ['Keyword', 'Total Extensions', 'All Extensions'],
      ...dataArray.map(r => [r.keyword, r.count, r.all_extensions.join(' ')])
    ].map(row => row.join(',')).join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `domain-results-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  const getPageNumbers = () => {
    const pages = [];
    const maxPagesToShow = 7;

    if (totalPages <= maxPagesToShow) {
      for (let i = 1; i <= totalPages; i++) pages.push(i);
    } else {
      if (currentPage <= 4) {
        for (let i = 1; i <= 5; i++) pages.push(i);
        pages.push('...');
        pages.push(totalPages);
      } else if (currentPage >= totalPages - 3) {
        pages.push(1);
        pages.push('...');
        for (let i = totalPages - 4; i <= totalPages; i++) pages.push(i);
      } else {
        pages.push(1);
        pages.push('...');
        for (let i = currentPage - 1; i <= currentPage + 1; i++) pages.push(i);
        pages.push('...');
        pages.push(totalPages);
      }
    }
    return pages;
  };

  if (dataArray.length === 0) {
    return (
      <div className="max-w-7xl mx-auto mb-12 text-center py-12">
        <div className={`text-lg ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
          No domain results found. Try a different search term.
        </div>
      </div>
    );
  }

  return (
    <>
      <div className="max-w-7xl mx-auto mb-12">
        {/* Header */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
          <div>
            <h2 className={`text-2xl sm:text-3xl font-bold mb-2 transition-colors duration-500 ${
              isDark ? 'text-white' : 'text-gray-900'
            }`}>
              Domain Results
            </h2>
            <p className={`text-sm transition-colors duration-500 ${
              isDark ? 'text-purple-200' : 'text-gray-600'
            }`}>
              Found {dataArray.length} keywords • Showing {startIndex + 1}-{Math.min(endIndex, dataArray.length)}
            </p>
          </div>

          <button
            onClick={handleExport}
            className={`flex items-center space-x-2 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:scale-105 ${
              isDark
                ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30 hover:bg-purple-500/30'
                : 'bg-purple-100 text-purple-700 border border-purple-200 hover:bg-purple-200'
            }`}
          >
            <Download className="w-4 h-4" />
            <span>Export CSV</span>
          </button>
        </div>

        {/* Table */}
        <div className={`rounded-xl border overflow-hidden transition-colors duration-500 ${
          isDark
            ? 'bg-black/40 backdrop-blur-xl border-purple-500/30'
            : 'bg-white border-purple-200 shadow-lg'
        }`}>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className={`border-b transition-colors duration-500 ${
                  isDark ? 'bg-purple-500/10 border-purple-500/30' : 'bg-purple-50 border-purple-200'
                }`}>
                  <th className="text-left p-4">
                    <button
                      onClick={() => handleSort('keyword')}
                      className={`flex items-center space-x-1 font-semibold transition-colors duration-300 hover:text-purple-500 ${
                        isDark ? 'text-white' : 'text-gray-900'
                      }`}
                    >
                      <span>Keyword</span>
                      <ArrowUpDown className="w-4 h-4" />
                    </button>
                  </th>
                  <th className="text-center p-4">
                    <button
                      onClick={() => handleSort('count')}
                      className={`flex items-center justify-center space-x-1 font-semibold mx-auto transition-colors duration-300 hover:text-purple-500 ${
                        isDark ? 'text-white' : 'text-gray-900'
                      }`}
                    >
                      <span>Total Extensions</span>
                      <ArrowUpDown className="w-4 h-4" />
                    </button>
                  </th>
                  <th className={`text-left p-4 font-semibold ${isDark ? 'text-white' : 'text-gray-900'}`}>
                    Domain Extensions
                  </th>
                </tr>
              </thead>
              <tbody>
                {currentResults.map((result, index) => {
                  const isExpanded = expandedRows.has(index);
                  const extensionsToShow = isExpanded
                    ? result.all_extensions
                    : result.all_extensions.slice(0, maxVisibleExtensions);
                  const hasMoreExtensions = result.all_extensions.length > maxVisibleExtensions;

                  return (
                    <tr
                      key={index}
                      className={`border-b transition-all duration-200 ${
                        isDark
                          ? 'border-purple-500/20 hover:bg-purple-500/5'
                          : 'border-purple-200 hover:bg-purple-50/50'
                      }`}
                    >
                      <td className="p-4">
                        <span className={`font-medium ${isDark ? 'text-purple-300' : 'text-purple-700'}`}>
                          {result.keyword}
                        </span>
                      </td>
                      <td className="p-4 text-center">
                        <span className={`font-semibold ${isDark ? 'text-white' : 'text-gray-900'}`}>
                          {result.count}
                        </span>
                      </td>
                      <td className="p-4">
                        <div className="flex flex-col gap-2">
                          <div className="flex flex-wrap gap-1.5">
                            {extensionsToShow.map((ext, i) => (
                              <span
                                key={i}
                                className={`px-2 py-0.5 rounded text-xs font-medium border ${
                                  isDark
                                    ? 'bg-purple-500/20 text-purple-300 border-purple-500/30'
                                    : 'bg-purple-100 text-purple-700 border-purple-200'
                                }`}
                              >
                                {ext}
                              </span>
                            ))}
                            {!isExpanded && hasMoreExtensions && (
                              <button
                                onClick={() => toggleRowExpansion(index)}
                                className={`px-2 py-0.5 rounded text-xs font-medium border transition-all duration-300 hover:scale-105 ${
                                  isDark
                                    ? 'bg-blue-500/30 text-blue-300 border-blue-500/50 hover:bg-blue-500/40'
                                    : 'bg-blue-100 text-blue-700 border-blue-300 hover:bg-blue-200'
                                }`}
                              >
                                +{result.all_extensions.length - maxVisibleExtensions} more
                              </button>
                            )}
                          </div>

                          {isExpanded && hasMoreExtensions && (
                            <div className="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                              <span className={`text-xs ${isDark ? 'text-purple-300' : 'text-purple-600'}`}>
                                Showing all {result.all_extensions.length} extensions
                              </span>
                              <button
                                onClick={() => toggleRowExpansion(index)}
                                className={`flex items-center gap-1 text-xs font-medium transition-all duration-300 hover:scale-105 ${
                                  isDark ? 'text-blue-300 hover:text-blue-200' : 'text-blue-600 hover:text-blue-800'
                                }`}
                              >
                                Show less
                                <ChevronUp className="w-3 h-3" />
                              </button>
                            </div>
                          )}
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            <p className={`text-sm ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
              Page {currentPage} of {totalPages}
            </p>

            <div className="flex items-center space-x-2">
              <button
                onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                disabled={currentPage === 1}
                className={`p-2 rounded-lg transition-all duration-300 ${
                  currentPage === 1
                    ? isDark ? 'bg-gray-800 text-gray-600 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : isDark ? 'bg-purple-500/20 text-purple-300 hover:bg-purple-500/30' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'
                }`}
              >
                <ChevronLeft className="w-5 h-5" />
              </button>

              {getPageNumbers().map((page, idx) => (
                page === '...' ? (
                  <span key={`ellipsis-${idx}`} className={`px-2 ${isDark ? 'text-purple-300' : 'text-gray-600'}`}>
                    ...
                  </span>
                ) : (
                  <button
                    key={page}
                    onClick={() => setCurrentPage(page)}
                    className={`px-4 py-2 rounded-lg font-medium transition-all duration-300 ${
                      page === currentPage
                        ? 'bg-gradient-to-r from-purple-500 to-blue-500 text-white'
                        : isDark
                          ? 'bg-purple-500/10 text-purple-300 hover:bg-purple-500/20'
                          : 'bg-purple-50 text-purple-700 hover:bg-purple-100'
                    }`}
                  >
                    {page}
                  </button>
                )
              ))}

              <button
                onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                disabled={currentPage === totalPages}
                className={`p-2 rounded-lg transition-all duration-300 ${
                  currentPage === totalPages
                    ? isDark ? 'bg-gray-800 text-gray-600 cursor-not-allowed' : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : isDark ? 'bg-purple-500/20 text-purple-300 hover:bg-purple-500/30' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'
                }`}
              >
                <ChevronRight className="w-5 h-5" />
              </button>
            </div>
          </div>
        )}
      </div>

      {/* AI Analysis Modal */}
      <AnimatePresence>
        {selectedKeyword && (
          <AIAnalysisModal
            keyword={selectedKeyword}
            onClose={() => setSelectedKeyword(null)}
            isDark={isDark}
          />
        )}
      </AnimatePresence>
    </>
  );
}

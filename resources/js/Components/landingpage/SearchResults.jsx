import DomainTable from './DomainTable';

export default function SearchResults({ query, data }) {
  const handleAIAnalysis = (keyword) => {
    alert(`Running AI analysis for: ${keyword}`);
  };

  const handleCheckExpiry = (keyword) => {
    alert(`Checking expiry date for: ${keyword}`);
  };

  return (
    <DomainTable
      results={data.data}
      onAIAnalysis={handleAIAnalysis}
      onCheckExpiry={handleCheckExpiry}
    />
  );
}

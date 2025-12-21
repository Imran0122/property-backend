'use client';

const StatCards = () => {
  const stats = [
    { title: 'For Sale', value: 12, color: 'bg-blue-100 text-blue-700' },
    { title: 'For Rent', value: 8, color: 'bg-yellow-100 text-yellow-700' },
    { title: 'Hot', value: 3, color: 'bg-red-100 text-red-700' },
    { title: 'Super Hot', value: 1, color: 'bg-green-100 text-green-700' },
  ];

  return (
    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      {stats.map((item) => (
        <div
          key={item.title}
          className="bg-white shadow-sm rounded-lg border border-gray-200 p-4 flex flex-col items-center justify-center"
        >
          <p className={`text-sm font-medium ${item.color}`}>{item.title}</p>
          <p className="text-2xl font-bold text-gray-800 mt-1">{item.value}</p>
        </div>
      ))}
    </div>
  );
};

export default StatCards;

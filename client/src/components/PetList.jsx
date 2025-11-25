export default function PetList({ pets }) {
  if (!pets.length) return <p>No pets found.</p>;

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      {pets.map((pet) => (
        <div key={pet.id} className="bg-white rounded shadow p-4 hover:shadow-lg transition">
          <h2 className="font-bold text-xl">{pet.name}</h2>
          <p>Species: {pet.species}</p>
          {pet.breed && <p>Breed: {pet.breed}</p>}
          {pet.age !== undefined && <p>Age: {pet.age}</p>}
          <p>Status: <span className={pet.status === 'adopted' ? 'text-green-600' : 'text-yellow-600'}>{pet.status}</span></p>
        </div>
      ))}
    </div>
  );
}

import React from 'react'

export default function PetCard({ pet, onOpen }) {
  const img = pet.photos?.length ? pet.photos[0].url : null

  return (
    <article className="bg-white rounded shadow p-4 hover:shadow-lg transition transform hover:-translate-y-0.5">
      {img ? (
        <img src={img} alt={pet.name} className="w-full h-40 object-cover rounded mb-3"/>
      ) : (
        <div className="w-full h-40 bg-gray-100 rounded mb-3 flex items-center justify-center text-gray-400">No image</div>
      )}

      <h3 className="font-semibold text-lg">{pet.name}</h3>
      <div className="text-sm text-gray-600">{pet.species} {pet.breed ? `· ${pet.breed}` : ''}</div>
      <div className="mt-2 flex items-center justify-between">
        <span className={`text-sm font-medium ${pet.status === 'adopted' ? 'text-green-600' : 'text-yellow-600'}`}>{pet.status}</span>
        <button onClick={() => onOpen(pet)} className="text-sm px-3 py-1 rounded bg-primary text-white hover:opacity-90">Details</button>
      </div>
    </article>
  )
}

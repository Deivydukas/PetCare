import React from 'react'

export default function PetModal({ pet, onClose }) {
  if (!pet) return null

  const img = pet.photos?.length ? pet.photos[0].url : null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50" role="dialog" aria-modal="true">
      <div className="bg-white rounded-lg max-w-2xl w-full p-4 relative">
        <button onClick={onClose} className="absolute right-3 top-3 text-gray-600">✕</button>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            {img ? <img src={img} alt={pet.name} className="w-full h-64 object-cover rounded" /> : <div className="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-400">No image</div>}
          </div>
          <div>
            <h2 className="text-2xl font-bold">{pet.name}</h2>
            <p className="text-sm text-gray-600 mt-1">{pet.species} {pet.breed && `· ${pet.breed}`}</p>
            <p className="mt-3">Age: {pet.age ?? 'N/A'}</p>
            <p className="mt-3">Status: <span className={pet.status === 'adopted' ? 'text-green-600' : 'text-yellow-600'}>{pet.status}</span></p>
            <div className="mt-4">
              <button className="bg-primary text-white px-4 py-2 rounded">Request adopt</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

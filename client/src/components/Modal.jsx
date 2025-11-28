// src/components/Modal/Modal.jsx
import React from 'react';

export default function Modal({ isOpen, onClose, children, title }) {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50" role="dialog" aria-modal="true">
      <div className="bg-white rounded-lg max-w-2xl w-full p-4 relative">
        {title && <h2 className="text-2xl font-bold mb-4">{title}</h2>}
        <button 
          onClick={onClose} 
          className="absolute right-3 top-3 text-gray-600 text-xl"
        >
          ✕
        </button>
        <div>
          {children}
        </div>
      </div>
    </div>
  );
}

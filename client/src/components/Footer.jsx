export default function Footer(){
  return (
    <footer className="bg-gray-900 text-white py-4">
      <div className="container mx-auto px-4 text-center">
        <div>© {new Date().getFullYear()} PetCare — All rights reserved</div>
      </div>
    </footer>
  )
}

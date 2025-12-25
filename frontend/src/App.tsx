import React from 'react'
import { BrowserRouter, Routes, Route } from 'react-router'
import './App.css'
import Home from '@/pages/Home'
import Login from '@/pages/Login'
import Register from '@/pages/Register'
import Dashboard from '@/pages/Dashboard'
import Game from '@/pages/Game'
import Ranking from '@/pages/Ranking'

export default function App() {
  return (
    <BrowserRouter>
      <div className="App">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/game" element={<Game />} />
          <Route path="/ranking" element={<Ranking />} />
        </Routes>
      </div>
    </BrowserRouter>
  )
}

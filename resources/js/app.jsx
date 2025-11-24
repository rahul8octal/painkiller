import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout.jsx';
import IdeaList from './pages/IdeaList.jsx';
import IdeaDetail from './pages/IdeaDetail.jsx';
import SubmitIdea from './pages/SubmitIdea.jsx';
import AdminDashboard from './pages/AdminDashboard.jsx';
import AdminEditIdea from './pages/AdminEditIdea.jsx';
import Pricing from './pages/Pricing.jsx';
import Signup from './pages/Signup.jsx';

import { AuthProvider } from './context/AuthContext.jsx';
import Login from './pages/Login.jsx';
import ProtectedRoute from './components/ProtectedRoute.jsx';

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    <Route path="/login" element={<Login />} />
                    <Route path="/signup" element={<Signup />} />
                    
                    <Route element={<Layout />}>
                        <Route path="/" element={<IdeaList />} />
                        <Route path="ideas/:id" element={<IdeaDetail />} />
                        <Route path="submit" element={<SubmitIdea />} />
                        
                        {/* Admin Routes */}
                        <Route path="admin" element={
                            <ProtectedRoute>
                                <AdminDashboard />
                            </ProtectedRoute>
                        } />
                        <Route path="admin/ideas/new" element={
                            <ProtectedRoute>
                                <AdminEditIdea />
                            </ProtectedRoute>
                        } />
                        <Route path="admin/ideas/:id/edit" element={
                            <ProtectedRoute>
                                <AdminEditIdea />
                            </ProtectedRoute>
                        } />
                        <Route path="/pricing" element={<Pricing />} /> {/* Added Pricing route */}

                        <Route path="*" element={<div className="p-10 text-center">
                            <h1 className="text-2xl font-bold text-slate-900">404 - Page Not Found</h1>
                            <p className="mt-2 text-slate-600">The page you are looking for does not exist.</p>
                        </div>} />
                    </Route>
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}

import { ErrorBoundary } from 'react-error-boundary';

function ErrorFallback({ error }) {
    return (
        <div className="p-10 text-red-600">
            <h1 className="text-2xl font-bold">Something went wrong.</h1>
            <pre className="mt-4 bg-red-50 p-4 rounded">{error.message}</pre>
        </div>
    );
}

const container = document.getElementById('app');
if (container) {
    console.log('Mounting React App...');
    const root = createRoot(container);
    root.render(
        <ErrorBoundary FallbackComponent={ErrorFallback} onError={(error) => console.error("React Error:", error)}>
            <App />
        </ErrorBoundary>
    );
} else {
    console.error('Root element #app not found!');
}

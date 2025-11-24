import React from 'react';
import { Outlet, Link, useLocation } from 'react-router-dom';
import { Lightbulb, PlusCircle, Search, Menu, X, Settings, LogOut, LogIn, CreditCard } from 'lucide-react';
import { useAuth } from '../context/AuthContext';

export default function Layout() {
    const location = useLocation();
    const [isMenuOpen, setIsMenuOpen] = React.useState(false);
    const { user, logout } = useAuth();

    const navItems = [
        { path: '/', label: 'Ideas Database', icon: Search },
        { path: '/submit', label: 'Analyze Idea', icon: PlusCircle },
    ];

    if (user && !user.is_admin) {
        navItems.push({ path: '/pricing', label: 'Pricing', icon: CreditCard });
    }

    if (user && user.is_admin) {
        navItems.push({ path: '/admin', label: 'Admin Panel', icon: Settings });
    }

    return (
        <div className="min-h-screen bg-slate-50">
            {/* Navigation */}
            <nav className="bg-white border-b border-slate-200 sticky top-0 z-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <Link to="/" className="flex-shrink-0 flex items-center">
                                <div className="bg-indigo-600 p-2 rounded-lg mr-3">
                                    <Lightbulb className="w-6 h-6 text-white" />
                                </div>
                                <span className="font-bold text-xl text-slate-900 tracking-tight">Painkiller Ideas</span>
                            </Link>
                            <div className="hidden sm:ml-8 sm:flex sm:space-x-8">
                                {navItems.map((item) => {
                                    const Icon = item.icon;
                                    const isActive = location.pathname === item.path;
                                    return (
                                        <Link
                                            key={item.path}
                                            to={item.path}
                                            className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200 ${
                                                isActive
                                                    ? 'border-indigo-500 text-slate-900'
                                                    : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'
                                            }`}
                                        >
                                            <Icon className={`w-4 h-4 mr-2 ${isActive ? 'text-indigo-500' : 'text-slate-400'}`} />
                                            {item.label}
                                        </Link>
                                    );
                                })}
                            </div>
                        </div>
                        
                        <div className="hidden sm:flex sm:items-center">
                            {user ? (
                                <div className="flex items-center space-x-4">
                                    <span className="text-sm text-slate-500">Hi, {user.name}</span>
                                    <button
                                        onClick={logout}
                                        className="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <LogOut className="w-3 h-3 mr-1" />
                                        Logout
                                    </button>
                                </div>
                            ) : (
                                <Link
                                    to="/login"
                                    className="text-slate-400 hover:text-slate-500"
                                    title="Admin Login"
                                >
                                    <LogIn className="w-5 h-5" />
                                </Link>
                            )}
                        </div>

                        <div className="-mr-2 flex items-center sm:hidden">
                            <button
                                onClick={() => setIsMenuOpen(!isMenuOpen)}
                                className="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                            >
                                {isMenuOpen ? (
                                    <X className="block h-6 w-6" />
                                ) : (
                                    <Menu className="block h-6 w-6" />
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Mobile menu */}
                {isMenuOpen && (
                    <div className="sm:hidden bg-white border-b border-slate-200">
                        <div className="pt-2 pb-3 space-y-1">
                            {navItems.map((item) => {
                                const Icon = item.icon;
                                const isActive = location.pathname === item.path;
                                return (
                                    <Link
                                        key={item.path}
                                        to={item.path}
                                        className={`block pl-3 pr-4 py-2 border-l-4 text-base font-medium ${
                                            isActive
                                                ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                                : 'border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700'
                                        }`}
                                        onClick={() => setIsMenuOpen(false)}
                                    >
                                        <div className="flex items-center">
                                            <Icon className={`w-5 h-5 mr-3 ${isActive ? 'text-indigo-500' : 'text-slate-400'}`} />
                                            {item.label}
                                        </div>
                                    </Link>
                                );
                            })}
                            {user ? (
                                <button
                                    onClick={() => { logout(); setIsMenuOpen(false); }}
                                    className="w-full text-left block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700"
                                >
                                    <div className="flex items-center">
                                        <LogOut className="w-5 h-5 mr-3 text-slate-400" />
                                        Logout
                                    </div>
                                </button>
                            ) : (
                                <Link
                                    to="/login"
                                    className="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700"
                                    onClick={() => setIsMenuOpen(false)}
                                >
                                    <div className="flex items-center">
                                        <LogIn className="w-5 h-5 mr-3 text-slate-400" />
                                        Admin Login
                                    </div>
                                </Link>
                            )}
                        </div>
                    </div>
                )}
            </nav>

            <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div className="px-4 py-6 sm:px-0">
                    <Outlet />
                </div>
            </main>
        </div>
    );
}

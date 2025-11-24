import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Check, Zap, Loader } from 'lucide-react';

export default function Pricing() {
    const [loading, setLoading] = useState(false);
    const [products, setProducts] = useState([]);
    const [fetching, setFetching] = useState(true);

    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = async () => {
        try {
            const response = await axios.get('/api/subscription/products');
            console.log('Products API Response:', response.data);
            setProducts(response.data.data || []);
        } catch (error) {
            console.error('Failed to fetch products:', error);
            setProducts([]); // Ensure it's an array on error
        } finally {
            setFetching(false);
        }
    };

    const handleSubscribe = async (variantId) => {
        setLoading(true);
        try {
            const response = await axios.post(`/api/checkout/${variantId}`);
            if (response.data.url) {
                window.location.href = response.data.url;
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert('Failed to start checkout. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    if (fetching) {
        return (
            <div className="flex justify-center items-center min-h-screen">
                <Loader className="w-8 h-8 text-indigo-600 animate-spin" />
            </div>
        );
    }

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div className="text-center mb-16">
                <h2 className="text-3xl font-bold text-slate-900 sm:text-4xl">
                    Simple, Transparent Pricing
                </h2>
                <p className="mt-4 text-xl text-slate-600">
                    Start validating your ideas today.
                </p>
            </div>

            <div className="grid md:grid-cols-3 gap-8">
                {/* Free Tier (Static) */}
                <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <h3 className="text-xl font-semibold text-slate-900">Starter</h3>
                    <div className="mt-4 flex items-baseline">
                        <span className="text-4xl font-bold text-slate-900">$0</span>
                        <span className="ml-1 text-slate-500">/mo</span>
                    </div>
                    <p className="mt-4 text-slate-500">Perfect for exploring the platform.</p>
                    <ul className="mt-6 space-y-4">
                        <li className="flex items-center text-slate-600">
                            <Check className="w-5 h-5 text-green-500 mr-2" />
                            3 Idea Validations / mo
                        </li>
                    </ul>
                    <button className="mt-8 w-full py-3 px-4 rounded-lg border border-indigo-600 text-indigo-600 font-medium hover:bg-indigo-50 transition-colors">
                        Current Plan
                    </button>
                </div>

                {/* Dynamic Products from Lemon Squeezy */}
                {Array.isArray(products) && products.length > 0 ? (
                    products.map((product) => {
                        // Assuming the first variant is the main one for simplicity
                        const variant = product.relationships?.variants?.data?.[0];
                        if (!variant) return null;

                        const price = product.attributes.price_formatted; // e.g. "$29.00"
                        const name = product.attributes.name;
                        const description = product.attributes.description || "Unlock full potential.";
                        const isPopular = name.toLowerCase().includes('pro');

                        return (
                            <div key={product.id} className={`rounded-2xl shadow-xl p-8 transform hover:scale-105 transition-transform duration-300 relative ${isPopular ? 'bg-indigo-600 text-white scale-105' : 'bg-white border border-slate-200'}`}>
                                {isPopular && (
                                    <div className="absolute top-0 right-0 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg uppercase tracking-wide">
                                        Popular
                                    </div>
                                )}
                                <h3 className={`text-xl font-semibold ${isPopular ? 'text-white' : 'text-slate-900'}`}>{name}</h3>
                                <div className="mt-4 flex items-baseline">
                                    <span className={`text-4xl font-bold ${isPopular ? 'text-white' : 'text-slate-900'}`}>{price}</span>
                                </div>
                                <div className={`mt-4 ${isPopular ? 'text-indigo-100' : 'text-slate-500'}`} dangerouslySetInnerHTML={{ __html: description }}></div>
                                
                                <button 
                                    onClick={() => handleSubscribe(variant.id)}
                                    disabled={loading}
                                    className={`mt-8 w-full py-3 px-4 rounded-lg font-bold transition-colors flex justify-center items-center ${
                                        isPopular 
                                        ? 'bg-white text-indigo-600 hover:bg-indigo-50' 
                                        : 'bg-slate-900 text-white hover:bg-slate-800'
                                    }`}
                                >
                                    {loading ? 'Processing...' : 'Subscribe Now'}
                                    {!loading && <Zap className="w-4 h-4 ml-2" />}
                                </button>
                            </div>
                        );
                    })
                ) : (
                    <div className="col-span-3 text-center text-slate-500">
                        {products === null ? 'Failed to load products.' : 'No products found.'}
                    </div>
                )}
            </div>
        </div>
    );
}

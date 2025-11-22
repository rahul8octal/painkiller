import React, { useState } from 'react';
import { Palette, Layout, Terminal, Megaphone, Copy, Check } from 'lucide-react';

export default function CreativeAssets({ assets }) {
    const [activeTab, setActiveTab] = useState('brand');
    const [copied, setCopied] = useState(null);

    if (!assets) return null;

    const copyToClipboard = (text, key) => {
        navigator.clipboard.writeText(text);
        setCopied(key);
        setTimeout(() => setCopied(null), 2000);
    };

    const tabs = [
        { id: 'brand', label: 'Brand Kit', icon: Palette },
        { id: 'ads', label: 'Ad Creatives', icon: Megaphone },
        { id: 'landing', label: 'Landing Page', icon: Layout },
        { id: 'dev', label: 'Dev Prompts', icon: Terminal },
    ];

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div className="border-b border-slate-200 bg-slate-50">
                <nav className="flex -mb-px" aria-label="Tabs">
                    {tabs.map((tab) => {
                        const Icon = tab.icon;
                        return (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`group relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center text-sm font-medium hover:bg-slate-100 focus:z-10 ${
                                    activeTab === tab.id
                                        ? 'text-indigo-600 bg-white border-b-2 border-indigo-600'
                                        : 'text-slate-500 hover:text-slate-700'
                                }`}
                            >
                                <div className="flex items-center justify-center">
                                    <Icon className={`w-4 h-4 mr-2 ${activeTab === tab.id ? 'text-indigo-600' : 'text-slate-400'}`} />
                                    {tab.label}
                                </div>
                            </button>
                        );
                    })}
                </nav>
            </div>

            <div className="p-6">
                {activeTab === 'brand' && (
                    <div className="space-y-8">
                        <div>
                            <h4 className="text-sm font-medium text-slate-500 uppercase tracking-wider mb-4">Color Palette</h4>
                            <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                                {Object.entries(assets.brand_package?.color_palette || {}).map(([name, hex]) => (
                                    <div key={name} className="space-y-2">
                                        <div 
                                            className="h-16 w-full rounded-lg shadow-sm border border-slate-200"
                                            style={{ backgroundColor: hex }}
                                        ></div>
                                        <div>
                                            <p className="text-xs font-medium text-slate-900 capitalize">{name}</p>
                                            <p className="text-xs text-slate-500 font-mono">{hex}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div>
                            <h4 className="text-sm font-medium text-slate-500 uppercase tracking-wider mb-4">Typography</h4>
                            <div className="grid md:grid-cols-3 gap-6">
                                <div className="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                    <p className="text-xs text-slate-500 mb-1">Headings</p>
                                    <p className="text-lg font-semibold text-slate-900">{assets.brand_package?.typography?.headings}</p>
                                </div>
                                <div className="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                    <p className="text-xs text-slate-500 mb-1">Body</p>
                                    <p className="text-lg text-slate-900">{assets.brand_package?.typography?.body}</p>
                                </div>
                                <div className="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                    <p className="text-xs text-slate-500 mb-1">Accent</p>
                                    <p className="text-lg font-mono text-slate-900">{assets.brand_package?.typography?.accent}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 className="text-sm font-medium text-slate-500 uppercase tracking-wider mb-4">Logo Concept</h4>
                            <div className="bg-slate-50 p-4 rounded-lg border border-slate-100 text-slate-700 text-sm leading-relaxed">
                                {assets.brand_package?.logo_prompt}
                            </div>
                        </div>
                    </div>
                )}

                {activeTab === 'ads' && (
                    <div className="grid gap-6 md:grid-cols-2">
                        {assets.ad_creatives?.map((ad, index) => (
                            <div key={index} className="border border-slate-200 rounded-xl p-5 hover:border-indigo-200 transition-colors">
                                <div className="flex justify-between items-start mb-3">
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        {ad.platform}
                                    </span>
                                </div>
                                <h4 className="font-bold text-slate-900 mb-2">{ad.headline}</h4>
                                <p className="text-sm text-slate-600 mb-4">{ad.body}</p>
                                <div className="flex justify-between items-center pt-4 border-t border-slate-100">
                                    <span className="text-xs font-medium text-indigo-600 uppercase tracking-wide">{ad.cta}</span>
                                    <button 
                                        onClick={() => copyToClipboard(ad.body, `ad-${index}`)}
                                        className="text-slate-400 hover:text-indigo-600"
                                    >
                                        {copied === `ad-${index}` ? <Check className="w-4 h-4" /> : <Copy className="w-4 h-4" />}
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {activeTab === 'landing' && (
                    <div className="space-y-6">
                        <div className="border border-slate-200 rounded-xl overflow-hidden">
                            <div className="bg-slate-50 px-4 py-2 border-b border-slate-200 flex items-center space-x-2">
                                <div className="w-3 h-3 rounded-full bg-red-400"></div>
                                <div className="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div className="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div className="p-8 text-center bg-white">
                                <h1 className="text-3xl font-extrabold text-slate-900 mb-4">
                                    {assets.landing_page?.hero_header}
                                </h1>
                                <p className="text-xl text-slate-600 mb-8 max-w-2xl mx-auto">
                                    {assets.landing_page?.subheader}
                                </p>
                                <button className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    {assets.landing_page?.cta}
                                </button>
                            </div>
                            <div className="bg-slate-50 p-8 border-t border-slate-200">
                                <div className="grid md:grid-cols-3 gap-8">
                                    {assets.landing_page?.features_list?.map((feature, index) => (
                                        <div key={index} className="flex items-start">
                                            <CheckCircle className="w-5 h-5 text-green-500 mr-2 flex-shrink-0" />
                                            <span className="text-sm text-slate-700">{feature}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {activeTab === 'dev' && (
                    <div className="space-y-4">
                        {assets.dev_prompts?.map((prompt, index) => (
                            <div key={index} className="bg-slate-900 rounded-xl overflow-hidden">
                                <div className="bg-slate-800 px-4 py-2 flex justify-between items-center">
                                    <span className="text-xs font-mono text-slate-400">{prompt.title}</span>
                                    <button 
                                        onClick={() => copyToClipboard(prompt.prompt_text, `dev-${index}`)}
                                        className="text-slate-400 hover:text-white"
                                    >
                                        {copied === `dev-${index}` ? <Check className="w-4 h-4" /> : <Copy className="w-4 h-4" />}
                                    </button>
                                </div>
                                <div className="p-4 font-mono text-sm text-green-400 whitespace-pre-wrap">
                                    {prompt.prompt_text}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

function CheckCircle({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
        </svg>
    );
}

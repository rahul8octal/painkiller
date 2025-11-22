import React from 'react';
import { Search, TrendingUp, BarChart2, DollarSign } from 'lucide-react';

export default function KeywordTrafficCard({ traffic }) {
    if (!traffic) return null;

    const { search_volume_overview, monthly_trends, top_keywords, market_opportunity } = traffic;

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div className="flex items-center mb-6">
                <div className="bg-indigo-100 p-2 rounded-lg mr-3">
                    <Search className="w-5 h-5 text-indigo-600" />
                </div>
                <h2 className="text-xl font-bold text-slate-900">Keyword Traffic & Trends</h2>
            </div>

            <div className="grid md:grid-cols-2 gap-8 mb-8">
                {/* Search Volume Overview */}
                <div>
                    <h3 className="text-sm font-semibold text-slate-500 mb-4">Search Volume Overview</h3>
                    <div className="bg-slate-50 rounded-lg p-6 border border-slate-100">
                        <div className="text-sm text-slate-500 mb-1">Total Monthly Searches</div>
                        <div className="flex items-baseline">
                            <span className="text-4xl font-bold text-slate-900">
                                {search_volume_overview?.total_monthly_searches?.toLocaleString() || 0}
                            </span>
                            {search_volume_overview?.growth_percentage > 0 && (
                                <span className="ml-3 text-sm font-medium text-green-600 flex items-center">
                                    <TrendingUp className="w-3 h-3 mr-1" />
                                    +{search_volume_overview.growth_percentage}% vs last year
                                </span>
                            )}
                        </div>
                    </div>

                    {/* Monthly Trends Chart (Simple CSS Bars) */}
                    <div className="mt-6">
                        <div className="flex items-end justify-between h-32 space-x-2">
                            {monthly_trends?.map((trend, index) => {
                                const maxVolume = Math.max(...monthly_trends.map(t => t.volume));
                                const height = maxVolume > 0 ? (trend.volume / maxVolume) * 100 : 0;
                                
                                return (
                                    <div key={index} className="flex flex-col items-center flex-1 group">
                                        <div className="relative w-full flex justify-center items-end h-full">
                                            <div 
                                                className="w-full bg-indigo-500 rounded-t-sm transition-all duration-300 group-hover:bg-indigo-600"
                                                style={{ height: `${height}%` }}
                                            ></div>
                                            <div className="absolute -top-8 bg-slate-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                                {trend.volume.toLocaleString()}
                                            </div>
                                        </div>
                                        <div className="mt-2 text-xs font-medium text-slate-500">{trend.month}</div>
                                        <div className={`text-[10px] mt-1 ${trend.growth >= 0 ? 'text-green-600' : 'text-red-500'}`}>
                                            {trend.growth > 0 ? '+' : ''}{trend.growth}%
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                        <div className="border-t border-slate-200 mt-2"></div>
                    </div>
                </div>

                {/* Top Keywords Performance */}
                <div>
                    <h3 className="text-sm font-semibold text-slate-500 mb-4">Top Keywords Performance</h3>
                    <div className="space-y-3">
                        {top_keywords?.map((kw, index) => (
                            <div key={index} className="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-lg shadow-sm hover:border-indigo-100 transition-colors">
                                <div>
                                    <div className="font-medium text-slate-900">{kw.keyword}</div>
                                    <div className="text-xs text-slate-500 mt-1 flex items-center">
                                        <BarChart2 className="w-3 h-3 mr-1" />
                                        {kw.volume}
                                    </div>
                                </div>
                                <div className="text-right">
                                    <div className="text-sm font-medium text-slate-600 flex items-center justify-end">
                                        <DollarSign className="w-3 h-3 mr-1 text-slate-400" />
                                        {kw.cpc}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Market Opportunity */}
                    <div className="mt-6 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <h4 className="text-sm font-bold text-indigo-900 mb-2 flex items-center">
                            <TrendingUp className="w-4 h-4 mr-2" />
                            Market Opportunity
                        </h4>
                        <p className="text-sm text-indigo-800 leading-relaxed">
                            {market_opportunity}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}

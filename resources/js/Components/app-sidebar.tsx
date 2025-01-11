import { Dot, GalleryVerticalEnd, Home } from 'lucide-react';
import * as React from 'react';

import { NavUser } from '@/Components/nav-user';
import { TeamSwitcher } from '@/Components/team-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenuButton,
    SidebarRail,
} from '@/Components/ui/sidebar';
import { usePage } from '@inertiajs/react';

// This is sample data.
const data = {
    teams: [
        {
            name: 'Default Team',
            logo: GalleryVerticalEnd,
            plan: 'Org name',
        },
    ],
};

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
    const user = usePage().props.auth.user;

    const userData = {
        name: user.name,
        email: user.email,
        avatar: '/avatars/shadcn.jpg',
    };

    return (
        <Sidebar collapsible="icon" {...props}>
            <SidebarHeader>
                <TeamSwitcher teams={data.teams} />
            </SidebarHeader>
            <SidebarContent>
                <SidebarGroup>
                    <SidebarMenuButton asChild>
                        <a href={route('dashboard')}>
                            <Home />
                            <span>Dashboard</span>
                            {route().current('dashboard') && <Dot />}
                        </a>
                    </SidebarMenuButton>
                </SidebarGroup>
            </SidebarContent>
            <SidebarFooter>
                <NavUser user={userData} />
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>
    );
}

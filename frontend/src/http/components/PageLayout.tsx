import React from 'react';
import { ScrollView, StyleProp, ViewStyle } from 'react-native';
import { Screen } from './Screen';
import { PageHeader } from './PageHeader';

type PageLayoutProps = {
  title: string;
  subtitle?: string;
  action?: React.ReactNode;
  children: React.ReactNode;
  contentStyle?: StyleProp<ViewStyle>;
  scrollRef?: React.RefObject<ScrollView>;
  onContentSizeChange?: (w: number, h: number) => void;
};

/**
 * Standard page wrapper: SafeArea + scroll + header (title/subtitle/action).
 * Use it to keep pages consistent and DRY.
 */
export function PageLayout({
  title,
  subtitle,
  action,
  children,
  contentStyle,
  scrollRef,
  onContentSizeChange,
}: PageLayoutProps) {
  return (
    <Screen
      scroll
      scrollRef={scrollRef}
      onContentSizeChange={onContentSizeChange}
      contentStyle={contentStyle}
    >
      <PageHeader title={title} subtitle={subtitle} action={action} />
      {children}
    </Screen>
  );
}


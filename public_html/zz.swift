//
//  CustomTabBar.swift
//  Duke
//
//  Created by Simbarashe Dombodzvuku on 2/22/25.
//

import SwiftUI
import AVKit
import Foundation
import FirebaseFirestore
import Combine
import Firebase
import FirebaseAuth
import Kingfisher
import SDWebImageSwiftUI
import FirebaseStorage
import PassKit
import CoreGraphics
import CoreMotion
import SpriteKit
import MapKit

struct ContentView: View {
    var body: some View {
        if #available(iOS 18.0, *) {
            CustomScrollView { progress in
                DummyScrollContent()
            } sheetContent: { progress in
                DummySheetContent()
            } bottomBar: { progress in
                Bottombar()
                    .padding(.bottom, 10)
            }
        } else {
            // Fallback on earlier versions
        }
    }
    
    /// Dummy Scroll Content
    @ViewBuilder
    func DummyScrollContent() -> some View {
        let fillColor: Color = .primary.opacity(0.07)
        VStack(alignment: .leading, spacing: 12) {
            Text("Home")
                .font(.largeTitle.bold())
                .padding(.bottom, 10)
            
            ForEach(1...10, id: \.self) { _ in
                VStack(alignment: .leading, spacing: 8) {
                    RoundedRectangle(cornerRadius: 10)
                        .fill(fillColor)
                        .frame(height: 220)
                    
                    HStack(spacing: 10) {
                        Circle()
                            .fill(fillColor)
                            .frame(width: 40, height: 40)
                        
                        VStack(alignment: .leading, spacing: 6) {
                            Capsule()
                                .fill(fillColor)
                                .frame(height: 10)
                                .padding(.trailing, 40)
                            
                            Capsule()
                                .fill(fillColor)
                                .frame(width: 170, height: 10)
                        }
                    }
                }
            }
        }
        .padding(15)
    }
    
    /// Dummy Sheet Content
    @ViewBuilder
    func DummySheetContent() -> some View {
        let fillColor: Color = .primary.opacity(0.07)
        
        VStack(alignment: .leading, spacing: 15) {
            Text("Featured")
                .font(.title.bold())
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding(.bottom, 10)
            
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
                
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
            }
            .frame(height: 180)
            
            Text("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.")
                .font(.callout)
                .multilineTextAlignment(.leading)
            
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
                
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
            }
            .frame(height: 180)
            
            Text("Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.")
                .font(.callout)
                .multilineTextAlignment(.leading)
                .padding(.top, 10)
        }
        .padding(15)
        .padding(.bottom, 50)
    }
    
    /// Bottom Bar
    @ViewBuilder
    func Bottombar() -> some View {
        Text("Home")
            .fontWeight(.medium)
            .padding(.vertical, 8)
            .padding(.horizontal, 15)
            .background(
                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                in: .capsule
            )
            .frame(maxWidth: .infinity)
            .overlay(alignment: .leading, content: {
                HStack {
                    Button {
                        
                    } label: {
                        Image(systemName: "xmark")
                            .fontWeight(.medium)
                            .frame(width: 45, height: 45)
                            .foregroundStyle(Color.primary)
                            .background(
                                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                                in: .circle
                            )
                    }
                    
                    Spacer()
                    
                    Button {
                        
                    } label: {
                        Image(systemName: "ellipsis")
                            .fontWeight(.medium)
                            .frame(width: 45, height: 45)
                            .foregroundStyle(Color.primary)
                            .background(
                                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                                in: .circle
                            )
                    }
                }
                .padding(.horizontal, 15)
            })
    }
}

#Preview {
    ContentView()
        .preferredColorScheme(.dark)
}

@available(iOS 16.0, *)
struct CustomTabBar: View {
    @StateObject var authService = AuthenticationService()
    @State var currentTab: CustomTab = .Home
    
    init() {
        UITabBar.appearance().isHidden = true
    }
    
    @Namespace var animation
    //current tab xvalue
    @State var currentXValue: CGFloat = 0
    
    var body: some View {
        if #available(iOS 26.0, *) {
            TabView(selection: $currentTab) {
                ChurchHomeView()
                    .tag(CustomTab.Home)
                
                ChurchPostsView()
                    .tag(CustomTab.Social)
                    .environmentObject(authService)
                
                SermonsView()
                    .tag(CustomTab.Sermons)
                
                GiveView()
                    .tag(CustomTab.Give)
                
                MoreView()
                    .tag(CustomTab.More)
                    .environmentObject(authService)
            }
            .overlay (
                HStack(spacing: 0) {
                    ForEach(CustomTab.allCases, id: \.rawValue) { tab in
                        TabButton(tab: tab)
                    }
                }
                    .padding(.vertical)
                    .padding(.bottom, safeArea().bottom == 0 ? 10 : (safeArea().bottom - 10))
                    .background(
                        MaterialEffect(style: .systemUltraThinMaterialDark)
                            .opacity(0.3)
                            .glassEffect(in: Rectangle())
                            .background(
                                LinearGradient(
                                    gradient: Gradient(colors: [.white.opacity(0.5), .clear, .clear, .clear, .white.opacity(0.5)]),
                                    startPoint: .leading,
                                    endPoint: .trailing
                                )
                            )
                            .clipShape(BottomCurve(currentXValue: currentXValue))
                            .overlay(content: {
                                BottomCurve(currentXValue: currentXValue)
                                    .stroke(
                                        LinearGradient(
                                            gradient: Gradient(colors: currentTab.gradientColors),
                                            startPoint: .topLeading,
                                            endPoint: .bottomTrailing
                                        ),
                                        lineWidth: 1.5
                                    )
                            })
                    )
                , alignment: .bottom
            )
            .ignoresSafeArea(.all, edges: .bottom)
            .preferredColorScheme(.dark)
        } else {
                // Fallback on earlier versions
            TabView(selection: $currentTab) {
                ChurchHomeView()
                    .tag(CustomTab.Home)
                
                ChurchPostsView()
                    .tag(CustomTab.Social)
                    .environmentObject(authService)
                
                SermonsView()
                    .tag(CustomTab.Sermons)
                
                GiveView()
                    .tag(CustomTab.Give)
                
                MoreView()
                    .tag(CustomTab.More)
                    .environmentObject(authService)
            }
            .overlay (
                HStack(spacing: 0) {
                    ForEach(CustomTab.allCases, id: \.rawValue) { tab in
                        TabButton(tab: tab)
                    }
                }
                    .padding(.vertical)
                    .padding(.bottom, safeArea().bottom == 0 ? 10 : (safeArea().bottom - 10))
                    .background(
                        MaterialEffect(style: .systemUltraThinMaterialDark)
                            .background(
                                LinearGradient(
                                    gradient: Gradient(colors: [.white.opacity(0.5), .clear, .clear, .clear, .white.opacity(0.5)]),
                                    startPoint: .leading,
                                    endPoint: .trailing
                                )
                            )
                            .clipShape(BottomCurve(currentXValue: currentXValue))
                            .overlay(content: {
                                BottomCurve(currentXValue: currentXValue)
                                    .stroke(
                                        LinearGradient(
                                            gradient: Gradient(colors: currentTab.gradientColors),
                                            startPoint: .topLeading,
                                            endPoint: .bottomTrailing
                                        ),
                                        lineWidth: 1.5
                                    )
                            })
                    )
                , alignment: .bottom
            )
            .ignoresSafeArea(.all, edges: .bottom)
            .preferredColorScheme(.dark)
        }
    }
    
    @ViewBuilder func TabButton(tab: CustomTab) -> some View {
        GeometryReader { proxy in
            Button {
                withAnimation(.spring()) {
                    currentTab = tab
                    //updating value
                    currentXValue = proxy.frame(in: .global).midX
                }
            } label: {
                if #available(iOS 26.0, *) {
                    Image(systemName: tab.rawValue)
                        .resizedToFit(width: 25, height: 25)
                        .frame(maxWidth: .infinity)
                        .foregroundColor(.white)
                        .padding(currentTab == tab ? .large : 0)
                        .if(currentTab == tab, transform: { thisView in
                            thisView
                                .glassEffect(in: Circle())
                                .matchedGeometryEffect(id: "TAB", in: animation)
                                .background(
                                    ZStack {
                                        if currentTab == tab {
                                            LiquidGlassMaterial()
                                                .opacity(0.7)
                                                .clipShape(Circle())
                                                .matchedGeometryEffect(id: "TAB", in: animation)
                                        }
                                    }
                                )
                        })
                        .contentShape(Rectangle())
                        .offset(y: currentTab == tab ? -50 : 0)
                } else {
                        // Fallback on earlier versions
                    Image(systemName: tab.rawValue)
                        .resizedToFit(width: 25, height: 25)
                        .frame(maxWidth: .infinity)
                        .foregroundColor(.white)
                        .padding(currentTab == tab ? .large : 0)
                        .background(
                            ZStack {
                                if currentTab == tab {
                                    LiquidGlassMaterial()
                                        .clipShape(Circle())
                                        .matchedGeometryEffect(id: "TAB", in: animation)
                                }
                            }
                        )
                        .contentShape(Rectangle())
                        .offset(y: currentTab == tab ? -50 : 0)
                }
            }
            //setting initial curve position
            .onAppear {
                if tab == CustomTab.allCases.first && currentXValue == 0 {
                    currentXValue = proxy.frame(in: .global).midX
                }
            }
        }
        .frame(height: 30)
    }
}

class MotionManager: ObservableObject {

    @Published var pitch: Double = 0.0
    @Published var roll: Double = 0.0
    @Published var rotation: Double = 0.0
    
    var motion: CMMotionManager

    init() {
        motion = CMMotionManager()
        motion.deviceMotionUpdateInterval = 1/60
        motion.startDeviceMotionUpdates(to: .main) { (motionData, error) in
            guard error == nil else { return }

            if let motionData = motionData {
                self.pitch = motionData.attitude.pitch
                self.roll = motionData.attitude.roll
                self.rotation = motionData.rotationRate.x
            }
        }
    }
}

@available(iOS 16.0, *)
struct CustomTabBar_Previews: PreviewProvider  {
    static var previews: some View {
        CustomTabBar()
            .environmentObject(AuthenticationService())
    }
}

enum CustomTab: String, CaseIterable {
    case Home = "house.fill"
    case Social = "person.3.sequence.fill" // Or "network"
    case Sermons = "mic.fill" // Or "play.rectangle.fill"
    case Give = "heart.fill" // Or "dollarsign.circle.fill"
    case More = "ellipsis.circle.fill"
    
    var gradientColors: [Color] {
        let opacity = 0.6
        switch self {
        case .Home:
            return [.clear, .white.opacity(opacity), .white.opacity(opacity), .white.opacity(opacity), .cyan.opacity(opacity)]
        case .Social:
            return [.mint.opacity(opacity), .clear, .mint.opacity(opacity), .mint.opacity(opacity), .mint.opacity(opacity)]
        case .Sermons:
            return [.green.opacity(opacity), .green.opacity(opacity), .clear, .green.opacity(opacity), .green.opacity(opacity)]
        case .Give:
            return [.teal.opacity(opacity), .teal.opacity(opacity), .teal.opacity(opacity), .clear, .teal.opacity(opacity),]
        case .More:
            return [.orange.opacity(opacity), .orange.opacity(opacity), .orange.opacity(opacity), .orange.opacity(opacity), .clear]
        }
    }
}

struct MaterialEffect: UIViewRepresentable {
    var style: UIBlurEffect.Style
    
    func makeUIView(context: Context) -> UIVisualEffectView {
        let view = UIVisualEffectView(effect: UIBlurEffect(style: style))
        return view
    }
    
    func updateUIView(_ uiView: UIVisualEffectView, context: Context) {
        
    }
}

struct BottomCurve: Shape {
    var currentXValue: CGFloat
    var cornerRadius: CGFloat = .large
    
        // Make the shape animatable by tracking the x-value of the curve.
    var animatableData: CGFloat {
        get { currentXValue }
        set { currentXValue = newValue }
    }
    
    func path(in rect: CGRect) -> Path {
        var path = Path()
        
            // Define the horizontal start and end points of the dip
        let dipStart = currentXValue - 50
        let dipEnd = currentXValue + 50
        
            // Define the points where the top corners would normally start and end
        let topLeftCornerEnd = rect.minX + cornerRadius
        let topRightCornerStart = rect.maxX - cornerRadius
        
            // --- Start drawing from the left side, moving clockwise ---
        
            // Move to the start of the bottom-left corner arc
        path.move(to: CGPoint(x: rect.minX, y: rect.maxY - cornerRadius))
        
            // Draw the bottom-left corner
        path.addArc(
            center: CGPoint(x: rect.minX + cornerRadius, y: rect.maxY - cornerRadius),
            radius: cornerRadius,
            startAngle: Angle(degrees: 180),
            endAngle: Angle(degrees: 90),
            clockwise: true
        )
        
            // Draw the bottom edge
        path.addLine(to: CGPoint(x: rect.maxX - cornerRadius, y: rect.maxY))
        
            // Draw the bottom-right corner
        path.addArc(
            center: CGPoint(x: rect.maxX - cornerRadius, y: rect.maxY - cornerRadius),
            radius: cornerRadius,
            startAngle: Angle(degrees: 90),
            endAngle: Angle(degrees: 0),
            clockwise: true
        )
        
            // Draw the right edge
        path.addLine(to: CGPoint(x: rect.maxX, y: rect.minY + cornerRadius))
        
            // --- Dynamic Top Edge ---
        
            // Check if there is space for the top-right rounded corner
        if dipEnd < topRightCornerStart {
                // Yes, there is space. Draw the rounded corner.
            path.addArc(
                center: CGPoint(x: topRightCornerStart, y: rect.minY + cornerRadius),
                radius: cornerRadius,
                startAngle: Angle(degrees: 0),
                endAngle: Angle(degrees: -90),
                clockwise: true
            )
                // Draw the straight line segment to the start of the dip
            path.addLine(to: CGPoint(x: dipEnd, y: rect.minY))
        } else {
                // No space. Draw a sharp corner instead.
            path.addLine(to: CGPoint(x: rect.maxX, y: rect.minY))
            path.addLine(to: CGPoint(x: dipEnd, y: rect.minY))
        }
        
            // --- The Dip --- (This part remains the same)
        let to1 = CGPoint(x: currentXValue, y: 35)
        let control1 = CGPoint(x: currentXValue + 25, y: 0)
        let control2 = CGPoint(x: currentXValue + 25, y: 35)
        
        let to2 = CGPoint(x: dipStart, y: 0)
        let control3 = CGPoint(x: currentXValue - 25, y: 35)
        let control4 = CGPoint(x: currentXValue - 25, y: 0)
        
            // We draw the curve from right to left to maintain a continuous path
        path.addCurve(to: to1, control1: control1, control2: control2)
        path.addCurve(to: to2, control1: control3, control2: control4)
        
            // --- Dynamic Top Edge (continued) ---
        
            // Check if there is space for the top-left rounded corner
        if dipStart > topLeftCornerEnd {
                // Yes, there is space. Draw the straight line and then the corner.
            path.addLine(to: CGPoint(x: topLeftCornerEnd, y: rect.minY))
            path.addArc(
                center: CGPoint(x: topLeftCornerEnd, y: rect.minY + cornerRadius),
                radius: cornerRadius,
                startAngle: Angle(degrees: -90),
                endAngle: Angle(degrees: -180),
                clockwise: true
            )
        } else {
                // No space. Draw a sharp corner.
            path.addLine(to: CGPoint(x: rect.minX, y: rect.minY))
            path.addLine(to: CGPoint(x: rect.minX, y: rect.maxY - cornerRadius))
        }
        
        path.closeSubpath()
        return path
    }
}

var width = UIScreen.main.bounds.width

struct ChurchHomeView: View {
    @EnvironmentObject var authService: AuthenticationService
    @StateObject var announcementCarouselVM = AnnouncementCarouselViewModel()
    @StateObject var upcomingEventsCarouselVM = UpcomingEventsCarouselViewModel()
    @State private var showAddAnnouncement = false
    @Namespace var animation
    @Namespace var animation2
    
    var body: some View {
        if #available(iOS 18.0, *) {
            CustomScrollView { progress in
                ScrollView {
                    VStack {
                        HStack {
                            GradientText(text: "Announcements")
                                .padding(.leading)
                            
                            Spacer()
                            
                            if announcementCarouselVM.swipedAnnouncement > 0 {
                                Button {
                                    // Action to reset the view
                                    resetViews(from: announcementCarouselVM)
                                } label: {
                                    Image(systemName: "arrow.counterclockwise")
                                        .font(.system(size: 20, weight: .semibold))
                                        .foregroundColor(.gray)
                                }
                                .padding(.trailing)
                            }
                            
                            if authService.userHasAdminRights() {
                                Button {
                                    showAddAnnouncement = true
                                } label: {
                                    Image(systemName: "plus.circle.fill")
                                        .font(.system(size: 24))
                                        .foregroundColor(.blue)
                                }
                                .padding(.trailing)
                            }
                        }
                        .padding()
                        
                        if #available(iOS 18.0, *) {
                            // Carousel
                            ScrollView(.horizontal, showsIndicators: false) {
                                CarouselView(carouselVM: announcementCarouselVM, animation: animation)
                            }
                        } else {
                            CarouselView(carouselVM: announcementCarouselVM, animation: animation)
                        }
                        
                        HStack {
                            GradientText(text: "Upcoming Events")
                                .foregroundColor(.black)
                                .padding(.leading)
                            
                            Spacer()
                            
                            if upcomingEventsCarouselVM.swipedEvent > 0 {
                                Button {
                                    // Action to reset the view
                                    resetViews(from: upcomingEventsCarouselVM)
                                } label: {
                                    Image(systemName: "arrow.counterclockwise")
                                        .font(.system(size: 20, weight: .semibold))
                                        .foregroundColor(.gray)
                                }
                                .padding(.trailing)
                            }
                        }
                        .padding()
                        
                        if #available(iOS 18.0, *) {
                            ScrollView(.horizontal, showsIndicators: false) {
                                CarouselView(carouselVM: upcomingEventsCarouselVM, animation: animation2)
                            }
                        } else {
                            CarouselView(carouselVM: upcomingEventsCarouselVM, animation: animation2)
                        }
                        
                        Spacer(minLength: getRect().height * 0.1)
                    }
                }
                .background(
                    ZStack {
                        Image("background1")
                            .resizable()
                            .aspectRatio(contentMode: .fill)
                            .ignoresSafeArea()
                        
                        LinearGradient(
                            gradient: Gradient(colors: [.red.opacity(0.1), .blue.opacity(0.5)]),
                            startPoint: .topLeading,
                            endPoint: .bottomTrailing
                        )
                        
                        VisualEffectBlur(blurStyle: .systemUltraThinMaterial, vibrancyStyle: .fill) {
                            
                        }
                    }
                        .ignoresSafeArea()
                )
                .sheet(item: $announcementCarouselVM.selectedAnnouncement) {
                    withAnimation(.spring()) {
                        announcementCarouselVM.selectedAnnouncement = nil
                        announcementCarouselVM.showAnnouncement = false
                        DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                            withAnimation(.easeIn) {
                                announcementCarouselVM.showContent.toggle()
                            }
                        }
                    }
                    for index in announcementCarouselVM.announcements.indices {
                        withAnimation(.spring()) {
                            announcementCarouselVM.announcements[index].offset = 0
                            announcementCarouselVM.swipedAnnouncement = 0
                        }
                    }
                } content: { selectedAnnouncement  in
                    AnnouncementDetailView(animation: animation)
                        .environmentObject(announcementCarouselVM)
                }
                .sheet(isPresented: $showAddAnnouncement) {
                    AddAnnouncementView()
                        .environmentObject(announcementCarouselVM)
                }
            } sheetContent: { progress in
                DummySheetContent()
            } bottomBar: { progress in
                Bottombar()
                    .padding(.bottom, 10)
            }
        } else {
            // Fallback on earlier versions
        }
    }
    
    /// Dummy Sheet Content
    @ViewBuilder
    func DummySheetContent() -> some View {
        let fillColor: Color = .primary.opacity(0.07)
        
        VStack(alignment: .leading, spacing: 15) {
            Text("Featured")
                .font(.title.bold())
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding(.bottom, 10)
            
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
                
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
            }
            .frame(height: 180)
            
            Text("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.")
                .font(.callout)
                .multilineTextAlignment(.leading)
            
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
                
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
            }
            .frame(height: 180)
            
            Text("Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.")
                .font(.callout)
                .multilineTextAlignment(.leading)
                .padding(.top, 10)
        }
        .padding(15)
        .padding(.bottom, 50)
    }
    
    /// Bottom Bar
    @ViewBuilder
    func Bottombar() -> some View {
        Text("Home")
            .fontWeight(.medium)
            .padding(.vertical, 8)
            .padding(.horizontal, 15)
            .background(
                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                in: .capsule
            )
            .frame(maxWidth: .infinity)
            .overlay(alignment: .leading, content: {
                HStack {
                    Button {
                        
                    } label: {
                        Image(systemName: "xmark")
                            .fontWeight(.medium)
                            .frame(width: 45, height: 45)
                            .foregroundStyle(Color.primary)
                            .background(
                                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                                in: .circle
                            )
                    }
                    
                    Spacer()
                    
                    Button {
                        
                    } label: {
                        Image(systemName: "ellipsis")
                            .fontWeight(.medium)
                            .frame(width: 45, height: 45)
                            .foregroundStyle(Color.primary)
                            .background(
                                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                                in: .circle
                            )
                    }
                }
                .padding(.horizontal, 15)
            })
    }
    
    @ViewBuilder func CarouselView(carouselVM: any ObservableObject, animation: Namespace.ID) -> some View {
        ZStack {
            if let announcementCarouselVM = carouselVM as? AnnouncementCarouselViewModel{
                ForEach(announcementCarouselVM.announcements.indices.reversed(), id: \.self) { index in
                    HStack {
                        AnnouncementCardView(announcement: announcementCarouselVM.announcements[index], animation: animation)
                            .frame(
                                width: getCardWidth(index: index, from: carouselVM),
                                height: getCardHeight(index: index, from: carouselVM)
                            )
                            .offset(x: getCardOffset(index: index, from: carouselVM))
                            .rotationEffect(.init(degrees: getCardRotation(index: index, from: carouselVM)))
                            .environmentObject(announcementCarouselVM)
                        
                        Spacer(minLength: 0)
                    }
                    .frame(height: 400)
                    .contentShape(Rectangle())
                    .offset(x: announcementCarouselVM.announcements[index].offset)
                    .gesture(
                        DragGesture(minimumDistance: 10)
                            .onChanged({ value in
                                if abs(value.translation.width) > abs(value.translation.height) {
                                    onChanged(value: value, index: index, from: carouselVM)
                                }
                            })
                            .onEnded({ value in
                                if abs(value.translation.width) > abs(value.translation.height) {
                                    onEnd(value: value, index: index, from: carouselVM)
                                }
                            })
                    )
                }
            }
            
            if let upcomingEventsCarouselViewModel = carouselVM as? UpcomingEventsCarouselViewModel{
                ForEach(upcomingEventsCarouselViewModel.events.indices.reversed(), id: \.self) { index in
                    HStack {
                        UpcomingEventCardView(event: upcomingEventsCarouselViewModel.events[index], animation: animation)
                            .frame(
                                width: getCardWidth(index: index, from: carouselVM),
                                height: getCardHeight(index: index, from: carouselVM)
                            )
                            .offset(x: getCardOffset(index: index, from: carouselVM))
                            .rotationEffect(.init(degrees: getCardRotation(index: index, from: carouselVM)))
                            .environmentObject(upcomingEventsCarouselViewModel)
                        
                        Spacer(minLength: 0)
                    }
                    .frame(height: 400)
                    .contentShape(Rectangle())
                    .offset(x: upcomingEventsCarouselViewModel.events[index].offset)
                    .gesture(
                        DragGesture(minimumDistance: 10)
                            .onChanged({ value in
                                if abs(value.translation.width) > abs(value.translation.height) {
                                    onChanged(value: value, index: index, from: carouselVM)
                                }
                            })
                            .onEnded({ value in
                                if abs(value.translation.width) > abs(value.translation.height) {
                                    onEnd(value: value, index: index, from: carouselVM)
                                }
                            })
                    )
                }
            }
        }
        .padding(.top, 25)
        .padding(.horizontal, 30)
        
    }
    
        // Get rotation when card is being swiped
    func getCardRotation(index: Int, from model: any ObservableObject) -> Double {
        let boxWidth = Double(width / 3)
        
            //Safely downcast to the different type of data,
        if let announcementCarouselVM = model as? AnnouncementCarouselViewModel {
            let offset = Double(announcementCarouselVM.announcements[index].offset)
            let angle: Double = 5
            return (offset / boxWidth) * angle
        }
        else if let upcomingEventsCarouselViewModel = model as? UpcomingEventsCarouselViewModel {
            let offset = Double(upcomingEventsCarouselViewModel.events[index].offset)
            let angle: Double = 5
            return (offset / boxWidth) * angle
        }
        
            //Default return to safe state
        return 0
    }
    
        // Get width and height for card
    func getCardHeight(index: Int, from model: any ObservableObject) -> CGFloat {
        let height: CGFloat = 400
        
        if let announcementCarouselVM = model as? AnnouncementCarouselViewModel {
            let cardHeight = index - announcementCarouselVM.swipedAnnouncement <= 2 ? CGFloat(index - announcementCarouselVM.swipedAnnouncement) * 35: 70
            return height - cardHeight
        }
        else if let upcomingEventsCarouselViewModel = model as? UpcomingEventsCarouselViewModel {
            let cardHeight = index - upcomingEventsCarouselViewModel.swipedEvent <= 2 ? CGFloat(index - upcomingEventsCarouselViewModel.swipedEvent) * 35: 70
            return height - cardHeight
        }
        
        return height
    }
    
    func getCardWidth(index: Int, from model: any ObservableObject) -> CGFloat {
        let boxWidth = UIScreen.main.bounds.width - 60 - 60
        return boxWidth
    }
    
        // Get offset
    func getCardOffset(index: Int, from model: any ObservableObject) -> CGFloat {
        if let announcementCarouselVM = model as? AnnouncementCarouselViewModel {
            return index - announcementCarouselVM.swipedAnnouncement <= 2 ? CGFloat(index - announcementCarouselVM.swipedAnnouncement) * 30 : 60
        }
        else if let upcomingEventsCarouselViewModel = model as? UpcomingEventsCarouselViewModel {
            return index - upcomingEventsCarouselViewModel.swipedEvent <= 2 ? CGFloat(index - upcomingEventsCarouselViewModel.swipedEvent) * 30 : 60
        }
        return 0
    }
    
        // Reset Views for both
    func resetViews(from model: any ObservableObject) {
        if let announcementCarouselVM = model as? AnnouncementCarouselViewModel {
            for index in announcementCarouselVM.announcements.indices {
                withAnimation(.spring()) {
                    announcementCarouselVM.announcements[index].offset = 0
                    announcementCarouselVM.swipedAnnouncement = 0
                }
            }
        }
        else if let upcomingEventsCarouselViewModel = model as? UpcomingEventsCarouselViewModel {
            for index in upcomingEventsCarouselViewModel.events.indices {
                withAnimation(.spring()) {
                    upcomingEventsCarouselViewModel.events[index].offset = 0
                    upcomingEventsCarouselViewModel.swipedEvent = 0
                }
            }
        }
    }
    
    func onChanged(value: DragGesture.Value, index: Int, from model: any ObservableObject) {
            // Only left swipe
        if let announcementCarouselVM = model as? AnnouncementCarouselViewModel {
            if value.translation.width < 0 {
                announcementCarouselVM.announcements[index].offset = value.translation.width
            }
        }
        else if let upcomingEventsCarouselViewModel = model as? UpcomingEventsCarouselViewModel {
            if value.translation.width < 0 {
                upcomingEventsCarouselViewModel.events[index].offset = value.translation.width
            }
        }
    }
    
    func onEnd(value: DragGesture.Value, index: Int, from model: any ObservableObject) {
        if let announcementCarouselVM = model as? AnnouncementCarouselViewModel {
            withAnimation {
                    // Check if there's more than one card left
                if -value.translation.width > width / 3 && announcementCarouselVM.swipedAnnouncement < announcementCarouselVM.announcements.count - 1 {
                    announcementCarouselVM.announcements[index].offset = -width
                    announcementCarouselVM.swipedAnnouncement += 1
                } else {
                        // If it's the last card, or the swipe wasn't far enough, reset its position
                    announcementCarouselVM.announcements[index].offset = 0
                }
            }
        }
        else if let upcomingEventsCarouselViewModel = model as? UpcomingEventsCarouselViewModel {
            withAnimation {
                    // Check if there's more than one card left
                if -value.translation.width > width / 3 && upcomingEventsCarouselViewModel.swipedEvent < upcomingEventsCarouselViewModel.events.count - 1 {
                    upcomingEventsCarouselViewModel.events[index].offset = -width
                    upcomingEventsCarouselViewModel.swipedEvent += 1
                } else {
                        // If it's the last card, or the swipe wasn't far enough, reset its position
                    upcomingEventsCarouselViewModel.events[index].offset = 0
                }
            }
        }
    }
}

struct SermonsView: View {
    @ObservedObject var sermonViewModel = SermonViewModel()
    @State private var showingAddSermonView = false
    @State private var selectedCategory: SermonCategory? = nil // Initially show all
    
    var body: some View {
        NavigationView {
            VStack { // Wrap everything in a VStack
                Picker("Category", selection: $selectedCategory) {
                    Text("All").tag(SermonCategory?(nil)) // Tag nil for "All"
                    ForEach(SermonCategory.allCases, id: \.self) { category in
                        Text(category.displayName).tag(SermonCategory?(category)) //Tag with the option category
                    }
                }
                .pickerStyle(.menu)
                .padding()
                
                List {
                    ForEach(filteredSermons) { sermon in
                        NavigationLink(destination: SermonDetailView(sermon: sermon)) {
                            SermonRow(sermon: sermon)
                        }
                    }
                    .onDelete(perform: deleteSermon)
                }
            }
            .navigationTitle("Sermons")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button {
                        showingAddSermonView = true
                    } label: {
                        Image(systemName: "plus")
                    }
                }
            }
            .background(
                ZStack {
                    Image("background1")
                        .resizable()
                        .aspectRatio(contentMode: .fill)
                        .edgesIgnoringSafeArea(.all)
                    Color("secondaryBackground")
                        .edgesIgnoringSafeArea(.all)
                        .opacity(0.7)
                    
                    VisualEffectBlur(blurStyle: .systemUltraThinMaterial, vibrancyStyle: .fill) {
                        
                    }
                }
            )
            .sheet(isPresented: $showingAddSermonView) {
                AddSermonView(sermonViewModel: sermonViewModel)
            }
            .onAppear {
                sermonViewModel.loadData()
            }
        }
    }
    
    var filteredSermons: [Sermon] {
        if let selectedCategory = selectedCategory {
            return sermonViewModel.sermons.filter { $0.category == selectedCategory }
        } else {
            return sermonViewModel.sermons // Show all sermons
        }
    }
    
    func deleteSermon(indexSet: IndexSet) {
        guard let index = indexSet.first else { return }
        let sermonToDelete = sermonViewModel.sermons[index]
        sermonViewModel.deleteSermon(sermon: sermonToDelete)
    }
}

struct AddSermonView: View {
    @Environment(\.dismiss) var dismiss // To dismiss the sheet
    @ObservedObject var sermonViewModel: SermonViewModel
    
    @State private var title: String = ""
    @State private var speaker: String = ""
    @State private var date: Date = Date()
    @State private var link: String = ""
    @State private var category: SermonCategory = .other
    
    var body: some View {
        NavigationView {
            Form {
                TextField("Title", text: $title)
                TextField("Speaker", text: $speaker)
                DatePicker("Date", selection: $date, displayedComponents: .date)
                TextField("YouTube Link", text: $link)
                Picker("Category", selection: $category) {
                    ForEach(SermonCategory.allCases, id: \.self) { category in
                        Text(category.displayName).tag(category)
                    }
                }
                .pickerStyle(.menu)
                
                Button("Add Sermon") {
                    let newSermon = Sermon(title: title, speaker: speaker, date: date, link: link, category: category)
                    sermonViewModel.addSermon(sermon: newSermon)
                    dismiss() // Dismiss the sheet after adding
                }
            }
            .navigationTitle("Add Sermon")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct SermonDetailView: View {
    let sermon: Sermon
    
    
    var body: some View {
        VStack {
            Text(sermon.title)
                .font(.largeTitle)
            Text("Speaker: \(sermon.speaker)")
                .font(.title2)
            Text("Date: \(sermon.formattedDate)")
                .font(.title3)
            Text("Category: \(sermon.category.displayName)")
                .font(.title3)
            
            // Category Image
            Image(sermon.category.imageName)
                .resizable()
                .scaledToFit()
                .frame(height: 200)
            
            Link("Watch on YouTube", destination: URL(string: sermon.link)!)
        }
        .padding()
    }
}

struct SermonRow: View {
    let sermon: Sermon
    
    var body: some View {
        VStack(alignment: .leading) {
            Text(sermon.title)
                .font(.headline)
            Text("Speaker: \(sermon.speaker)")
                .font(.subheadline)
            Text("Date: \(sermon.formattedDate)") // Use the formatted date
                .font(.caption)
                .foregroundColor(.secondary)
        }
    }
}

struct GiveView: View {
    var body: some View {
        NavigationView {
            VStack {
                Text("Give to New Life Church")
                    .font(.title)
                    .padding()
                
                Text("Your generosity helps us continue our mission.")
                    .multilineTextAlignment(.center)
                    .padding()
                
                // Add buttons or links to online giving platforms
                Button(action: {
                    // Open giving website or app link
                    if let url = URL(string: "https://example.com/giving") {  // Replace with New Life's giving link
                        UIApplication.shared.open(url)
                    }
                }) {
                    Text("Give Online")
                        .padding()
                        .background(Color.blue)
                        .foregroundColor(.white)
                        .cornerRadius(10)
                }
                .padding()
            }
            .navigationTitle("Give")
        }
    }
}

struct ConnectView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var cellGroupViewModel = CellGroupViewModel()
    @State private var showingAddGroup = false
    @State var startAnimation = false //Pulse animation
    @State private var mapRegion = MKCoordinateRegion(
        center: CLLocationCoordinate2D(latitude: -17.8277, longitude: 31.0534),
        span: MKCoordinateSpan(latitudeDelta: 0.05, longitudeDelta: 0.05)
    )

    var body: some View {
        ZStack {
            // Map Background
            Map(coordinateRegion: $mapRegion)
                .ignoresSafeArea()

            VStack {
                // Nav bar...
                HStack(spacing: 10) {
                    GradientText(text: "Nearby Cell Groups")
                        .font(.title2)

                    Spacer()

                    Button {
                        showingAddGroup = true
                    } label: {
                        Image(systemName: "plus")
                            .font(.system(size: 22, weight: .semibold))
                            .foregroundColor(.black)
                    }
                }
                .padding()
                .padding(.top, safeArea().top)
                .background(Color.white)

                ZStack {
                    Circle()
                        .stroke(Color.gray.opacity(0.6))
                        .frame(width: 130, height: 130)
                        .scaleEffect(startAnimation ? 3.3 : 0)
                        .opacity(startAnimation ? 0 : 1)
                        .animation(
                            Animation.linear(duration: 1.7)
                                .repeatForever(autoreverses: false)
                        )

                    Circle()
                        .fill(Color.white)
                        .frame(width: 130, height: 130)
                        .shadow(color: Color.black.opacity(0.07), radius: 5, x: 5, y: 5)

                    // Show found cell groups
                    ForEach(cellGroupViewModel.cellGroups) { cellGroup in
                        Text(cellGroup.name)
                           .font(.headline)
                            .foregroundColor(.white)
                            .padding(8)
                            .background(Color.blue.opacity(0.7))
                            .clipShape(RoundedRectangle(cornerRadius: 10))
                            .offset(randomOffset()) // Replace randomOffset with a function to get coords
                    }

                }
                .frame(maxHeight: .infinity)
            }
            .ignoresSafeArea()
            .background(Color.black.opacity(0.05).ignoresSafeArea())
            .onAppear {
                // Start animating when view loads
                withAnimation(Animation.linear(duration: 1.7).repeatForever(autoreverses: false)) {
                    startAnimation = true
                }
                cellGroupViewModel.loadData()
            }
            .sheet(isPresented: $showingAddGroup) {
                AddCellGroupView(cellGroupViewModel: cellGroupViewModel)
                    .environmentObject(authService)
            }

            //Display Error Messages
            if let errorMessage = authService.errorMessage {
                Text(errorMessage)
                    .foregroundColor(.red)
                    .padding()
            }
        }
    }

    // Helper function to generate random offsets for the cell group labels
    func randomOffset() -> CGSize {
        let x = CGFloat.random(in: -80...80)
        let y = CGFloat.random(in: -80...80)
        return CGSize(width: x, height: y)
    }
}

@available(iOS 16.0, *)
struct MoreView: View {
    @EnvironmentObject var authService: AuthenticationService
    
    var body: some View {
        NavigationView {
            List {
                NavigationLink(destination: PrayerRequestView().environmentObject(authService)) {
                    Text("Prayer Requests")
                }
                
                NavigationLink(destination: StaffDirectoryView()) {
                    Text("Staff Directory")
                }
                
                NavigationLink(destination: TestimonyListView(testimonyViewModel: TestimonyViewModel())) {
                    Text("Testimonies")
                }
                
                NavigationLink(destination: SongListView(songViewModel: SongViewModel())) {
                    Text("Songs")
                }
                
                NavigationLink(destination: AttendanceView()) {
                    Text("Attendance")
                }
                
                NavigationLink(destination: LessonListView(lessonViewModel: LessonViewModel())) {
                    Text("Lessons")
                }
                
                NavigationLink(destination: BookListView()) {
                    Text("Books")
                }
                
                NavigationLink(destination: ConnectView()) {
                    Text("Connect")
                }
                // Add more options here (e.g., About Us, Events Calendar)
            }
            .navigationTitle("More")
        }
    }
}

struct Sermon: Identifiable, Codable {
    @DocumentID var id: String? // Automatically populated by Firestore
    var title: String
    var speaker: String
    var date: Date
    var link: String // YouTube Link
    var category: SermonCategory
    
    // Use this to make the date more readable for debugging
    var formattedDate: String {
        let formatter = DateFormatter()
        formatter.dateStyle = .medium
        return formatter.string(from: date)
    }
    
    enum CodingKeys: String, CodingKey {
        case id
        case title
        case speaker
        case date
        case link
        case category
    }
}

enum SermonCategory: String, Codable, CaseIterable {
    case forgiveness = "Forgiveness"
    case marriage = "Marriage"
    case faith = "Faith"
    case prayer = "Prayer"
    case other = "Other"
    
    var displayName: String {
        return self.rawValue
    }
    
    var imageName: String {
        switch self {
        case .forgiveness: return "background1"
        case .marriage: return "background2"
        case .faith: return "background3"
        case .prayer: return "background4"
        case .other: return "background5"
        }
    }
}

class SermonViewModel: ObservableObject {
    @Published var sermons: [Sermon] = []
    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration? // To prevent memory leaks
    
    init() {
        loadData()
    }
    
    func loadData() {
        listenerRegistration = db.collection("sermons")
            .order(by: "date", descending: true) // Order by date, newest first
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    print("Error getting documents: \(error)")
                    return
                }
                
                self.sermons = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: Sermon.self)
                } ?? []
            }
    }
    
    deinit {
        // Remove the listener when the view model is deallocated
        listenerRegistration?.remove()
    }
    
    
    func addSermon(sermon: Sermon) {
        do {
            _ = try db.collection("sermons").addDocument(from: sermon)
        } catch {
            print("Error saving sermon: \(error)")
        }
    }
    
    func updateSermon(sermon: Sermon) {
        guard let sermonID = sermon.id else { return } // Need the document ID
        do {
            try db.collection("sermons").document(sermonID).setData(from: sermon)
        } catch {
            print("Error updating sermon: \(error)")
        }
    }
    
    func deleteSermon(sermon: Sermon) {
        guard let sermonID = sermon.id else { return } // Need the document ID
        db.collection("sermons").document(sermonID).delete() { error in
            if let error = error {
                print("Error removing document: \(error)")
            } else {
                print("Document successfully removed!")
            }
        }
    }
}

struct PrayerRequest: Identifiable, Codable {
    @DocumentID var id: String?
    var userId: String
    var requestText: String
    var timestamp: Date = Date()
    var isResolved: Bool = false // Add a flag to mark if resolved or not
    
    // Add a formatted date string
    var formattedDate: String {
        let formatter = DateFormatter()
        formatter.dateStyle = .medium
        formatter.timeStyle = .short
        return formatter.string(from: timestamp)
    }
}

class PrayerRequestViewModel: ObservableObject {
    @Published var prayerRequests: [PrayerRequest] = []
    @Published var errorMessage: String? = nil // For displaying error messages
    
    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?
    
    init() {
        loadData()
    }
    
    func loadData() {
        listenerRegistration = db.collection("prayerRequests")
            .order(by: "timestamp", descending: true)
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting prayer requests: \(error.localizedDescription)"
                    print("Error getting prayer requests: \(error)")
                    return
                }
                
                self.prayerRequests = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: PrayerRequest.self)
                } ?? []
            }
    }
    
    deinit {
        listenerRegistration?.remove()
    }
    
    func addPrayerRequest(requestText: String) {
        guard let userId = Auth.auth().currentUser?.uid else {
            errorMessage = "Not signed in"
            return // User must be signed in
        }
        
        let newRequest = PrayerRequest(userId: userId, requestText: requestText)
        do {
            _ = try db.collection("prayerRequests").addDocument(from: newRequest)
        } catch {
            errorMessage = "Error adding prayer request: \(error.localizedDescription)"
            print("Error adding prayer request: \(error)")
        }
    }
    
    func deletePrayerRequest(prayerRequest: PrayerRequest) {
        guard let requestId = prayerRequest.id else { return }
        
        db.collection("prayerRequests").document(requestId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting prayer request: \(error.localizedDescription)"
                print("Error deleting prayer request: \(error)")
            }
        }
    }
    
    func markAsResolved(prayerRequest: PrayerRequest) {
        guard let requestId = prayerRequest.id else { return }
        
        // Create a copy of the prayer request with the isResolved flag toggled
        var updatedRequest = prayerRequest
        updatedRequest.isResolved.toggle()
        
        do {
            try db.collection("prayerRequests").document(requestId).setData(from: updatedRequest)
        } catch {
            self.errorMessage = "Error marking as resolved: \(error.localizedDescription)"
            print("Error marking as resolved: \(error)")
        }
    }
}

struct AddPrayerRequestView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var prayerRequestViewModel: PrayerRequestViewModel
    @State private var newRequestText = ""
    
    var body: some View {
        NavigationView {
            VStack {
                TextEditor(text: $newRequestText)
                    .border(Color.gray, width: 1) // Optional border
                    .padding()
                
                Button("Submit Prayer Request") {
                    prayerRequestViewModel.addPrayerRequest(requestText: newRequestText)
                    dismiss()
                }
                .padding()
                .background(Color.blue)
                .foregroundColor(.white)
                .cornerRadius(8)
                .disabled(newRequestText.isEmpty)
            }
            .padding(.bottom, 70) // Add extra padding to bottom so that TextEditor is not covered by tab bar
            .navigationTitle("Add Prayer Request")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

@available(iOS 16.0, *)
struct PrayerRequestView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var prayerRequestViewModel = PrayerRequestViewModel()
    @State private var showingSignIn = false
    @State private var showingSignUp = false
    @State private var showingAddRequest = false //New sheet to present add prayer
    
    var body: some View {
        NavigationView {
            VStack {
                if authService.isLoggedIn {
                    Button {
                        showingAddRequest = true
                    } label: {
                        Text("Add Prayer Request")
                    }
                    
                    // Logged-in user
                    List {
                        ForEach(prayerRequestViewModel.prayerRequests) { request in
                            PrayerRequestRow(prayerRequest: request, prayerRequestViewModel: prayerRequestViewModel)
                        }
                        .onDelete { indexSet in
                            // Implement delete
                            for index in indexSet {
                                let prayerRequest = prayerRequestViewModel.prayerRequests[index]
                                prayerRequestViewModel.deletePrayerRequest(prayerRequest: prayerRequest)
                            }
                        }
                    }
                    
                } else {
                    // Not logged in
                    VStack {
                        Text("Please sign in to submit a prayer request.")
                            .padding()
                        
                        HStack {
                            Button("Sign In") {
                                showingSignIn = true
                            }
                            .padding()
                            .background(Color.blue)
                            .foregroundColor(.white)
                            .cornerRadius(8)
                            
                            Button("Sign Up") {
                                showingSignUp = true
                            }
                            .padding()
                            .background(Color.green)
                            .foregroundColor(.white)
                            .cornerRadius(8)
                        }
                    }
                }
            }
            .navigationTitle("Prayer Requests")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    if authService.isLoggedIn {
                        Button("Sign Out") {
                            authService.signOut()
                        }
                    }
                }
            }
            .sheet(isPresented: $showingSignIn) {
                SignInView(authService: authService)
            }
            .sheet(isPresented: $showingSignUp) {
                SignUpView(authService: authService)
            }
            .sheet(isPresented: $showingAddRequest) {
                AddPrayerRequestView(prayerRequestViewModel: prayerRequestViewModel)
                    .environmentObject(authService)
            }
            .onAppear {
                prayerRequestViewModel.loadData()
            }
        }
    }
}

struct PrayerRequestRow: View {
    let prayerRequest: PrayerRequest
    @ObservedObject var prayerRequestViewModel: PrayerRequestViewModel
    
    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(prayerRequest.requestText)
                    .font(.headline)
                Text("Submitted on: \(prayerRequest.formattedDate)")
                    .font(.subheadline)
                    .foregroundColor(.secondary)
            }
            
            Spacer() // Push everything to the left
            
            // Resolved Button
            Button(action: {
                prayerRequestViewModel.markAsResolved(prayerRequest: prayerRequest)
            }) {
                Image(systemName: prayerRequest.isResolved ? "checkmark.circle.fill" : "circle")
                    .foregroundColor(prayerRequest.isResolved ? .green : .gray)
            }
        }
    }
}

class AuthenticationService: ObservableObject {
    @Published var isLoggedIn = false
    @Published var user: AppUser? = nil // Stores the Firebase AppUser
    @Published var errorMessage: String? = nil // For displaying error messages
    @Published var staffCategory: StaffCategory? = nil // Add StaffCategory property

    private var db = Firestore.firestore()
    private var cancellables: Set<AnyCancellable> = []

    init() {
        // Observe authentication state changes
        Auth.auth().addStateDidChangeListener { auth, user in
            self.user = nil
            self.staffCategory = nil
            self.isLoggedIn = user != nil

            if let user = user {
                self.fetchUserProfile(userId: user.uid)
                print("Auth State Changed - Logged In: \(self.isLoggedIn)")
            }
        }
    }

    // MARK: - Authentication Actions---------------------------------

    func signUp(email: String, password: String, firstName: String, lastName: String, birthday: Date, phoneNumber: String, city: String, country: String, membershipType: AppUser.MembershipType, imageUrl: String) {
            Auth.auth().createUser(withEmail: email, password: password) { result, error in
                if let error = error {
                    self.errorMessage = "Sign-up failed: \(error.localizedDescription)"
                } else {
                    // Sign-up successful, now create user profile in Firestore
                    self.errorMessage = nil
                    print("Sign-up successful")
                    if let user = result?.user {
                        self.createUserProfile(userId: user.uid, email: email, firstName: firstName, lastName: lastName, birthday: birthday, phoneNumber: phoneNumber, city: city, country: country, membershipType: membershipType, imageUrl: imageUrl)
                    }
                }
            }
        }

    func signIn(email: String, password: String) {
        Auth.auth().signIn(withEmail: email, password: password) { result, error in
            if let error = error {
                self.errorMessage = "Sign-in failed: \(error.localizedDescription)"
            } else {
                // Sign-in successful
                self.errorMessage = nil
                if let user = Auth.auth().currentUser {
                    self.fetchUserProfile(userId: user.uid)
                }
                print("Sign-in successful")
            }
        }
    }

    func signOut() {
        do {
            try Auth.auth().signOut()
            self.user = nil
            self.staffCategory = nil
            print("Signed out")
        } catch {
            self.errorMessage = "Sign-out failed: \(error.localizedDescription)"
            print("Sign-out failed: \(error)")
        }
    }

    // MARK: - Firestore Interaction---------------------------------

    func createUserProfile(userId: String, email: String, firstName: String, lastName: String, birthday: Date, phoneNumber: String, city: String, country: String, membershipType: AppUser.MembershipType, imageUrl: String) {
        let newUser = AppUser(id: userId, firstName: firstName, lastName: lastName, birthday: birthday, phoneNumber: phoneNumber, email: email, city: city, country: country, membershipType: membershipType, imageUrl: imageUrl)
        
        do {
            _ = try db.collection("users").document(userId).setData(from: newUser)
        } catch {
            errorMessage = "Error creating user profile: \(error.localizedDescription)"
            print("Error creating user profile: \(error)")
        }
    }

    func fetchUserProfile(userId: String) {
        db.collection("users").document(userId).getDocument { (document, error) in
            if let error = error {
                self.errorMessage = "Error fetching user profile: \(error.localizedDescription)"
                print("Error fetching user profile: \(error)")
                return
            }

            if let document = document, document.exists {
                do {
                    self.user = try document.data(as: AppUser.self)
                    if let user = self.user {
                        self.loadStaffCategory(user: user)
                    }
                    print("User Profile fetched successfully")
                } catch {
                    self.errorMessage = "Error decoding user profile: \(error.localizedDescription)"
                    print("Error decoding user profile: \(error)")
                }
            } else {
                print("User profile does not exist")
            }
        }
    }

    func loadStaffCategory(user: AppUser){
        db.collection("staff")
            .whereField("email", isEqualTo: user.email) // Assuming you store user's email in staff document
            .getDocuments { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error fetching staff data: \(error.localizedDescription)"
                    print("Error fetching staff data: \(error)")
                    return
                }

                guard let document = querySnapshot?.documents.first else {
                    self.staffCategory = nil // Default category
                    print("No staff document found for user")
                    return
                }

                do {
                    let staffMember = try document.data(as: StaffMember.self)
                    self.staffCategory = staffMember.category
                    print("Staff Category: \(String(describing: self.staffCategory))")
                } catch {
                    self.errorMessage = "Error decoding staff data: \(error.localizedDescription)"
                    print("Error decoding staff data: \(error)")
                    self.staffCategory = nil //Default category
                }
            }
    }


    // Helper function to check if user has admin rights
    func userHasAdminRights() -> Bool {
        guard let category = staffCategory else { return false }
        return [.admin, .pastor, .ministryLeader].contains(category)
    }

    func getMembershipTypeText() -> String{
        return self.user?.membershipType.rawValue ?? "Other"
    }
}

struct SignUpView: View {
    @Environment(\.dismiss) var dismiss
    @ObservedObject var authService: AuthenticationService
    @State private var email = ""
    @State private var password = ""
    @State private var firstName = ""
    @State private var lastName = ""
    @State private var birthday = Date()
    @State private var phoneNumber = ""
    @State private var city = ""
    @State private var country = ""
    @State private var imageUrl = ""
    @State private var membershipType: AppUser.MembershipType = .other

    var body: some View {
        NavigationView {
            Form {
                TextField("First Name", text: $firstName)
                TextField("Last Name", text: $lastName)
                DatePicker("Birthday", selection: $birthday, displayedComponents: .date)
                TextField("Phone Number", text: $phoneNumber)
                    .keyboardType(.phonePad)
                TextField("Email", text: $email)
                    .keyboardType(.emailAddress)
                    .autocapitalization(.none)
                TextField("City", text: $city)
                TextField("Country", text: $country)
                TextField("ImageUrl", text: $imageUrl)
                Picker("Membership Type", selection: $membershipType) {
                    ForEach(AppUser.MembershipType.allCases, id: \.self) { type in
                        Text(type.displayName).tag(type)
                    }
                }
                .pickerStyle(.menu)
                SecureField("Password", text: $password)

                Button("Sign Up") {
                    authService.signUp(email: email, password: password, firstName: firstName, lastName: lastName, birthday: birthday, phoneNumber: phoneNumber, city: city, country: country, membershipType: membershipType, imageUrl: imageUrl)
                }
                .frame(maxWidth: .infinity)
                .listRowBackground(Color.blue.opacity(0.2))

                if let errorMessage = authService.errorMessage {
                    Text(errorMessage)
                        .foregroundColor(.red)
                        .padding()
                }
            }
            .navigationTitle("Sign Up")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct SignInView: View {
    @Environment(\.dismiss) var dismiss
    @ObservedObject var authService: AuthenticationService
    @State private var email = ""
    @State private var password = ""
    
    var body: some View {
        NavigationView {
            Form {
                TextField("Email", text: $email)
                    .keyboardType(.emailAddress)
                    .autocapitalization(.none)
                SecureField("Password", text: $password)
                
                Button("Sign In") {
                    authService.signIn(email: email, password: password)
                }
                .frame(maxWidth: .infinity)
                .listRowBackground(Color.blue.opacity(0.2))
                
                if let errorMessage = authService.errorMessage {
                    Text(errorMessage)
                        .foregroundColor(.red)
                        .padding()
                }
            }
            .navigationTitle("Sign In")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct StaffMember: Identifiable, Codable {
    @DocumentID var id: String?
    var name: String
    var title: String
    var email: String
    var phoneNumber: String?
    var bio: String?
    var imageUrl: String?
    var category: StaffCategory
}

struct ErrorMessage: Identifiable {
    let id = UUID()
    let message: String
}

class StaffDirectoryViewModel: ObservableObject {
    @Published var staffMembers: [StaffMember] = []
    @Published var errorMessage: ErrorMessage? = nil

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        listenerRegistration = db.collection("staff")
            .order(by: "name") // Order alphabetically by name
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = ErrorMessage(message: "Error getting staff members: \(error.localizedDescription)")
                    print("Error getting staff members: \(error)")
                    return
                }

                self.staffMembers = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: StaffMember.self)
                } ?? []
            }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addStaffMember(staffMember: StaffMember) {
        do {
            _ = try db.collection("staff").addDocument(from: staffMember)
        } catch {
            errorMessage = ErrorMessage(message: "Error adding staff member: \(error.localizedDescription)")
            print("Error adding staff member: \(error)")
        }
    }

    func updateStaffMember(staffMember: StaffMember) {
        guard let staffId = staffMember.id else { return }
        do {
            try db.collection("staff").document(staffId).setData(from: staffMember)
        } catch {
            errorMessage = ErrorMessage(message: "Error updating staff member: \(error.localizedDescription)")
            print("Error updating staff member: \(error)")
        }
    }

    func deleteStaffMember(staffMember: StaffMember) {
        guard let staffId = staffMember.id else { return }
        db.collection("staff").document(staffId).delete() { error in
            if let error = error {
                self.errorMessage = ErrorMessage(message: "Error deleting staff member: \(error.localizedDescription)")
                print("Error deleting staff member: \(error)")
            }
        }
    }
}

struct StaffDirectoryView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var staffDirectoryViewModel = StaffDirectoryViewModel()
    @State private var showingAddStaffMember = false
    @State private var selectedCategory: StaffCategory? = nil

    var body: some View {
        NavigationView {
            VStack {
                Picker("Category", selection: $selectedCategory) {
                    Text("All").tag(StaffCategory?(nil))
                    ForEach(StaffCategory.allCases, id: \.self) { category in
                        Text(category.displayName).tag(StaffCategory?(category))
                    }
                }
                .pickerStyle(.menu)
                .padding()

                List {
                    ForEach(filteredStaffMembers) { member in
                        NavigationLink(destination: StaffMemberDetailView(staffMember: member)) {
                            StaffMemberRow(staffMember: member)
                        }
                    }
                    .onDelete(perform: deleteStaffMember)
                }
            }
            .navigationTitle("Staff Directory")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    // Check if the user has permission to add staff (you need to implement role-based access control)
                    if userHasAdminRights { // Replace with your actual permission check
                        Button {
                            showingAddStaffMember = true
                        } label: {
                            Image(systemName: "plus")
                        }
                    }
                }
            }
            .sheet(isPresented: $showingAddStaffMember) {
                AddStaffMemberView(staffDirectoryViewModel: staffDirectoryViewModel)
            }
            .onAppear {
                staffDirectoryViewModel.loadData()
            }
            .alert(item: $staffDirectoryViewModel.errorMessage) { message in
                Alert(title: Text("Error"), message: Text(message.message), dismissButton: .default(Text("OK")))
            }
        }
    }

    // Computed property for filtered staff members
    private var filteredStaffMembers: [StaffMember] {
        if let selectedCategory = selectedCategory {
            return staffDirectoryViewModel.staffMembers.filter { $0.category == selectedCategory }
        } else {
            return staffDirectoryViewModel.staffMembers
        }
    }

    //Dummy check since the admin rights is not set
    var userHasAdminRights: Bool {
        return authService.isLoggedIn
    }

    func deleteStaffMember(indexSet: IndexSet) {
        guard let index = indexSet.first else { return }
        let memberToDelete = staffDirectoryViewModel.staffMembers[index]
        staffDirectoryViewModel.deleteStaffMember(staffMember: memberToDelete)
    }
}

struct AddStaffMemberView: View {
    @Environment(\.dismiss) var dismiss
    @ObservedObject var staffDirectoryViewModel: StaffDirectoryViewModel
    @State private var name = ""
    @State private var title = ""
    @State private var email = ""
    @State private var phoneNumber = ""
    @State private var bio = ""
    @State private var imageUrl = ""
    @State private var category: StaffCategory = .other // ADDED THIS LINE

    var body: some View {
        NavigationView {
            Form {
                Section(header: Text("Basic Information")) {
                    TextField("Name", text: $name)
                    TextField("Title", text: $title)
                }

                Section(header: Text("Contact Information")) {
                    TextField("Email", text: $email)
                        .keyboardType(.emailAddress)
                        .autocapitalization(.none)
                    TextField("Phone Number", text: $phoneNumber)
                        .keyboardType(.phonePad)
                }

                Section(header: Text("Details")) {
                    TextEditor(text: $bio)
                        .frame(height: 100) // Adjustable height
                    TextField("Image URL", text: $imageUrl)
                }

                Section(header: Text("Category")) {
                    Picker("Category", selection: $category) {
                        ForEach(StaffCategory.allCases, id: \.self) { category in
                            Text(category.displayName).tag(category)
                        }
                    }
                    .pickerStyle(.menu)
                }

                Button("Add Staff Member") {
                    let newStaffMember = StaffMember(name: name, title: title, email: email, phoneNumber: phoneNumber, bio: bio, imageUrl: imageUrl, category: category)
                    staffDirectoryViewModel.addStaffMember(staffMember: newStaffMember)
                    dismiss()
                }
                .frame(maxWidth: .infinity)
                .listRowBackground(Color.green.opacity(0.2))
            }
            .navigationTitle("Add Staff Member")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct StaffMemberRow: View {
    let staffMember: StaffMember
    
    var body: some View {
        HStack {
            // Display the image, if available
            if let imageUrl = staffMember.imageUrl, let url = URL(string: imageUrl) {
                KFImage(url)
                    .resizable()
                    .scaledToFit()
                    .frame(width: 50, height: 50)
                    .clipShape(Circle())
            } else {
                Image(systemName: "person.circle.fill") // Placeholder
                    .resizable()
                    .frame(width: 50, height: 50)
            }
            
            VStack(alignment: .leading) {
                Text(staffMember.name)
                    .font(.headline)
                Text(staffMember.title)
                    .font(.subheadline)
            }
        }
    }
}

struct StaffMemberDetailView: View {
    let staffMember: StaffMember
    
    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 15) {
                // Display the image, if available
                if let imageUrl = staffMember.imageUrl, let url = URL(string: imageUrl) {
                    KFImage(url)
                        .resizable()
                        .scaledToFit()
                        .frame(width: 150, height: 150)
                        .clipShape(Circle())
                        .padding(.bottom)
                } else {
                    Image(systemName: "person.circle.fill") // Placeholder
                        .resizable()
                        .frame(width: 150, height: 150)
                        .padding(.bottom)
                }
                
                Text(staffMember.name)
                    .font(.largeTitle)
                    .fontWeight(.bold)
                
                Text(staffMember.title)
                    .font(.title2)
                    .foregroundColor(.secondary)
                
                Divider()
                
                Text("Contact Information")
                    .font(.title3)
                    .fontWeight(.semibold)
                
                Text("Email: \(staffMember.email)")
                    .foregroundColor(.blue)
                    .onTapGesture {
                        // Open email
                        let emailURL = URL(string: "mailto:\(staffMember.email)")!
                        UIApplication.shared.open(emailURL)
                    }
                
                if let phoneNumber = staffMember.phoneNumber {
                    Text("Phone: \(phoneNumber)")
                        .foregroundColor(.blue)
                        .onTapGesture {
                            // Open phone
                            let phoneURL = URL(string: "tel:\(phoneNumber)")!
                            UIApplication.shared.open(phoneURL)
                        }
                }
                
                Divider()
                
                if let bio = staffMember.bio {
                    Text("Biography")
                        .font(.title3)
                        .fontWeight(.semibold)
                    Text(bio)
                }
                
                Spacer() // Push content to top
            }
            .padding()
        }
        .navigationTitle("Staff Details")
    }
}

enum StaffCategory: String, Codable, CaseIterable {
    case pastor = "Pastor"
    case ministryLeader = "Ministry Leader"
    case admin = "Admin"
    case worshipLeader = "Worship Leader"
    case childrenMinistry = "Children's Ministry"
    case youthMinistry = "Youth Ministry"
    case other = "Other"
    
    var displayName: String {
        return self.rawValue
    }
}

struct Testimony: Identifiable, Codable {
    @DocumentID var id: String?
    var userId: String
    var title: String
    var story: String
    var date: Date = Date()
    var isApproved: Bool = false // For moderation

    // Formatted Date
    var formattedDate: String {
        let formatter = DateFormatter()
        formatter.dateStyle = .medium
        return formatter.string(from: date)
    }
}

class TestimonyViewModel: ObservableObject {
    @Published var testimonies: [Testimony] = []
    @Published var errorMessage: String? = nil
    @Published var showAllTestimonies = false  // Added this line

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        var query: Query = db.collection("testimonies")
            .order(by: "date", descending: true)

        if !showAllTestimonies {
            query = query.whereField("isApproved", isEqualTo: true)
        }

        listenerRegistration = query.addSnapshotListener { (querySnapshot, error) in
            if let error = error {
                self.errorMessage = "Error getting testimonies: \(error.localizedDescription)"
                print("Error getting testimonies: \(error)")
                return
            }

            self.testimonies = querySnapshot?.documents.compactMap { document in
                try? document.data(as: Testimony.self)
            } ?? []
        }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addTestimony(title: String, story: String) {
        guard let userId = Auth.auth().currentUser?.uid else {
            errorMessage = "Not signed in"
            return // User must be signed in
        }

        let newTestimony = Testimony(userId: userId, title: title, story: story)
        do {
            _ = try db.collection("testimonies").addDocument(from: newTestimony)
        } catch {
            errorMessage = "Error adding testimony: \(error.localizedDescription)"
            print("Error adding testimony: \(error)")
        }
    }

    func updateTestimony(testimony: Testimony) {
        guard let testimonyId = testimony.id else { return }
        do {
            try db.collection("testimonies").document(testimonyId).setData(from: testimony)
        } catch {
            errorMessage = "Error updating testimony: \(error.localizedDescription)"
            print("Error updating testimony: \(error)")
        }
    }

    func deleteTestimony(testimony: Testimony) {
        guard let testimonyId = testimony.id else { return }
        db.collection("testimonies").document(testimonyId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting testimony: \(error.localizedDescription)"
                print("Error deleting testimony: \(error)")
            }
        }
    }
}

struct AddTestimonyView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var testimonyViewModel: TestimonyViewModel
    @State private var title = ""
    @State private var story = ""

    var body: some View {
        NavigationView {
            Form {
                TextField("Title", text: $title)

                Section {
                    TextEditor(text: $story)
                        .frame(height: 200)
                }

                Button("Submit Testimony") {
                    guard (authService.user?.email) != nil else {
                        print("User not logged in")
                        return
                    }
                    testimonyViewModel.addTestimony(title: title, story: story)
                    dismiss()
                }
            }
            .navigationTitle("Share Your Testimony")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct TestimonyListView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var testimonyViewModel: TestimonyViewModel
    @State private var showingAddTestimony = false

    var body: some View {
        NavigationView {
            List {
                ForEach(testimonyViewModel.testimonies) { testimony in
                    NavigationLink(destination: TestimonyDetailView(testimony: testimony)) {
                        TestimonyRow(testimonyViewModel: testimonyViewModel, testimony: testimony)
                            .frame(height: 100)
                    }
                }
            }
            .navigationTitle("Testimonies")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button {
                        showingAddTestimony = true
                    } label: {
                        Image(systemName: "plus")
                    }
                }
            }
            .sheet(isPresented: $showingAddTestimony) {
                AddTestimonyView(testimonyViewModel: testimonyViewModel)
            }
            .onAppear {
                testimonyViewModel.loadData()
            }
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    if authService.userHasAdminRights() {
                        Button(action: {
                            testimonyViewModel.showAllTestimonies.toggle()
                            testimonyViewModel.loadData()
                        }) {
                            Text(testimonyViewModel.showAllTestimonies ? "Show Approved" : "Show All")
                        }
                    }
                }
            }
        }
    }
}

struct TestimonyRow: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var testimonyViewModel: TestimonyViewModel
    let testimony: Testimony

    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(testimony.title)
                    .font(.headline)
                Text(testimony.formattedDate)
                    .font(.subheadline)
                    .foregroundColor(.secondary)
            }

            Spacer() // Push content to the left

            if authService.userHasAdminRights() {
                Button {
                    var mutableTestimony = testimony  // Create a mutable copy
                    mutableTestimony.isApproved.toggle()  // Toggle the property

                    testimonyViewModel.updateTestimony(testimony: mutableTestimony)
                } label: {
                    Image(systemName: testimony.isApproved ? "checkmark.circle.fill" : "circle")
                }
            }
        }
    }
}

struct TestimonyDetailView: View {
    let testimony: Testimony

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 15) {
                Text(testimony.title)
                    .font(.largeTitle)
                    .fontWeight(.bold)
                Text(testimony.formattedDate)
                    .font(.subheadline)
                    .foregroundColor(.secondary)
                Divider()
                Text(testimony.story)
                    .font(.body)
            }
            .padding()
        }
        .navigationTitle("Testimony")
    }
}

enum SongCategory: String, Codable, CaseIterable {
    case praise = "Praise Songs"
    case worship = "Worship Songs"

    var displayName: String {
        return self.rawValue
    }
}

struct Song: Identifiable, Codable {
    @DocumentID var id: String?
    var title: String
    var lyrics: String
    var addedBy: String
    var category: SongCategory
    var createdAt: Date = Date()
    var isApproved: Bool = false // Set to `false` by default. Will be updated to `true` by approver (admin/worship Leader)
    var approvedBy: String? = nil// name of the approver (admin/worship Leader)
}

class SongViewModel: ObservableObject {
    @Published var songs: [Song] = []
    @Published var errorMessage: String? = nil
    @Published var showAllSongs = false  // Added this line
    @Published var isLoading = false // Add loading state

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        isLoading = true // Set loading state to true

        var query: Query = db.collection("songs")
            .order(by: "title", descending: false)

        if !showAllSongs {
            query = query.whereField("isApproved", isEqualTo: true)
        }

        listenerRegistration = query.addSnapshotListener { [self] (querySnapshot, error) in
            isLoading = false // Set loading state to false

            if let error = error {
                self.errorMessage = "Error getting songs: \(error.localizedDescription)"
                print("Error getting songs: \(error)")
                return
            }

            self.songs = querySnapshot?.documents.compactMap { document in
                try? document.data(as: Song.self)
            } ?? []
        }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addSong(title: String, lyrics: String, category: SongCategory, addedBy: String) {
        guard let userId = Auth.auth().currentUser?.uid else {
            errorMessage = "Not signed in"
            return
        }
        let newSong = Song(title: title, lyrics: lyrics, addedBy: addedBy, category: category)
        do {
            _ = try db.collection("songs").addDocument(from: newSong)
        } catch {
            errorMessage = "Error adding song: \(error.localizedDescription)"
            print("Error adding song: \(error)")
        }
    }

    func updateSong(song: Song) {
        guard let songId = song.id else { return }
        do {
            try db.collection("songs").document(songId).setData(from: song)
        } catch {
            errorMessage = "Error updating song: \(error.localizedDescription)"
            print("Error updating song: \(error)")
        }
    }

    func deleteSong(song: Song) {
        guard let songId = song.id else { return }
        db.collection("songs").document(songId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting song: \(error.localizedDescription)"
                print("Error deleting song: \(error)")
            }
        }
    }
}

struct AddSongView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var songViewModel: SongViewModel
    @State private var title = ""
    @State private var lyrics = ""
    @State private var category: SongCategory = .praise

    var body: some View {
        NavigationView {
            Form {
                Section(header: Text("Song Information")) {
                    TextField("Title", text: $title)
                }

                Section(header: Text("Lyrics")) {
                    TextEditor(text: $lyrics)
                        .frame(height: 200)
                }

                Section(header: Text("Category")) {
                    Picker("Category", selection: $category) {
                        ForEach(SongCategory.allCases, id: \.self) { category in
                            Text(category.displayName).tag(category)
                        }
                    }
                    .pickerStyle(.menu)
                }

                Button("Add Song") {
                    if let userName = authService.user?.email {
                        songViewModel.addSong(title: title, lyrics: lyrics, category: category, addedBy: userName)
                        dismiss()
                    } else {
                        // Handle the case where the user's name is not available
                        print("Name is not available.")
                    }

                }
                .frame(maxWidth: .infinity) // Make button fill width
                .listRowBackground(Color.green.opacity(0.2)) // Make button stand out
            }
            .navigationTitle("Add Song")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct SongListView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var songViewModel: SongViewModel
    @State private var showingAddSong = false

    var body: some View {
        NavigationView {
            List {
                ForEach(songViewModel.songs) { song in
                    NavigationLink(destination: SongDetailView(song: song)) {
                        SongRow(songViewModel: songViewModel, song: song)
                            .frame(height: 100)
                    }
                }
            }
            .navigationTitle("Songs")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    if authService.userHasAdminRights() || authService.staffCategory == .worshipLeader {
                        Button {
                            showingAddSong = true
                        } label: {
                            Image(systemName: "plus")
                        }
                    }
                }
            }
            .sheet(isPresented: $showingAddSong) {
                AddSongView(songViewModel: songViewModel)
                    .environmentObject(authService)
            }
            .onAppear {
                songViewModel.loadData()
            }
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    if authService.userHasAdminRights() || authService.staffCategory == .worshipLeader {
                        Button(action: {
                            songViewModel.showAllSongs.toggle()
                            songViewModel.loadData()
                        }) {
                            Text(songViewModel.showAllSongs ? "Show Approved" : "Show All")
                        }
                    }
                }
            }
        }
    }
}

struct SongRow: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var songViewModel: SongViewModel
    let song: Song

    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(song.title)
                    .font(.headline)
                Text(song.addedBy)
                    .font(.subheadline)
                    .foregroundColor(.secondary)
            }

            Spacer() // Push content to the left

            if authService.userHasAdminRights() || authService.staffCategory == .worshipLeader {
                Button {
                    var mutableSong = song
                    mutableSong.isApproved.toggle()

                    if mutableSong.isApproved {
                        mutableSong.approvedBy = authService.user?.email // Set approver's name
                    } else {
                        mutableSong.approvedBy = nil // Clear approver's name
                    }
                    songViewModel.updateSong(song: mutableSong)
                } label: {
                    Image(systemName: song.isApproved ? "checkmark.circle.fill" : "circle")
                }
            }
        }
    }
}

struct SongDetailView: View {
    let song: Song

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 15) {
                Text(song.title)
                    .font(.largeTitle)
                    .fontWeight(.bold)

                Text("Category: \(song.category.displayName)")
                    .font(.headline)
                    .foregroundColor(.secondary)

                Text("Added by: \(song.addedBy)")
                    .font(.headline)
                    .foregroundColor(.secondary)

                if let approvedBy = song.approvedBy {
                    Text("Approved by: \(approvedBy)")
                        .font(.headline)
                        .foregroundColor(.secondary)
                }

                Divider()

                Text("Lyrics:")
                    .font(.title2)
                Text(song.lyrics)
                    .font(.body)

                Spacer()
            }
            .padding()
        }
        .navigationTitle("Song Details")
    }
}

enum Gender: String, Codable, CaseIterable {
    case male = "Male"
    case female = "Female"

    var displayName: String {
        return self.rawValue
    }
}

struct Child: Identifiable, Codable {
    @DocumentID var id: String?
    var name: String
    var age: Int
    var grade: String? // Optional
    var gender: Gender
}

struct AddChildView: View {
    @Environment(\.dismiss) var dismiss
    @ObservedObject var childViewModel: ChildViewModel
    @State private var name = ""
    @State private var age = ""
    @State private var grade = "" // New State variable for Grade
    @State private var gender: Gender = .male // ADDED THIS LINE, default to male

    var body: some View {
        NavigationView {
            Form {
                TextField("Name", text: $name)
                TextField("Age", text: $age)
                    .keyboardType(.numberPad)

                TextField("Grade (Optional)", text: $grade) // TextField for Grade

                // ADDED THIS PICKER
                Picker("Gender", selection: $gender) {
                    ForEach(Gender.allCases, id: \.self) { gender in
                        Text(gender.displayName).tag(gender)
                    }
                }
                .pickerStyle(.menu)

                Button("Add Child") {
                    if let ageInt = Int(age) {
                        let newChild = Child(name: name, age: ageInt, grade: grade, gender: gender) // ADDED GENDER
                        childViewModel.addChild(child: newChild)
                        dismiss()
                    } else {
                        // Handle invalid age input
                        print("Invalid age input")
                    }
                }
            }
            .navigationTitle("Add Child")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct AttendanceView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var childViewModel = ChildViewModel()
    @ObservedObject var attendanceViewModel = AttendanceViewModel()
    @State private var showingAddChild = false
    @State private var isDatePickerVisible = true // Add a state variable

    var body: some View {
        NavigationView {
            VStack {
                // Toggle to show or hide the date picker
                Toggle(isOn: $isDatePickerVisible) {
                    Text("Show Date Picker")
                }
                .padding(.horizontal)

                // Conditional display of the date picker
                if isDatePickerVisible {
                    DatePicker("Date", selection: $attendanceViewModel.selectedDate, displayedComponents: .date)
                        .datePickerStyle(.graphical)
                        .onChange(of: attendanceViewModel.selectedDate) { _ in
                            attendanceViewModel.loadData()
                        }
                        .padding()
                }

                List {
                    ForEach(childViewModel.children) { child in
                        AttendanceRow(child: child, attendanceViewModel: attendanceViewModel, isPresent: isChildPresent(childId: child.id ?? ""), teacherName: authService.user?.email ?? "Unknown")
                    }
                }
            }
            .navigationTitle("Attendance")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    if authService.staffCategory == .childrenMinistry || authService.userHasAdminRights() {
                        Button {
                            showingAddChild = true
                        } label: {
                            Image(systemName: "plus")
                        }
                    }
                }
            }
            .sheet(isPresented: $showingAddChild) {
                AddChildView(childViewModel: childViewModel)
            }
            .onAppear {
                childViewModel.loadData()
                attendanceViewModel.loadData()
            }
        }
    }

    // Helper function to check if a child is present on the selected date
    func isChildPresent(childId: String) -> Bool {
        return attendanceViewModel.attendanceRecords.contains { record in
            record.childId == childId
        }
    }
}

struct AttendanceRow: View {
    let child: Child
    @ObservedObject var attendanceViewModel: AttendanceViewModel
    @State var isPresent: Bool
    var teacherName: String

    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(child.name)
                Text("(\(child.gender.displayName))") //Display the gender in Row
                    .font(.caption)
                    .foregroundColor(.secondary)
            }
            Spacer()
            Toggle(isOn: $isPresent) {
                Text(isPresent ? "Present" : "Absent")
            }
            .onChange(of: isPresent) { newValue in
                if newValue {
                    // Record attendance
                    attendanceViewModel.recordAttendance(childId: child.id ?? "", isPresent: true, notes: nil, teacherName: teacherName)
                } else {
                    // Delete record
                    let recordToDelete = attendanceViewModel.attendanceRecords.first { record in
                        record.childId == child.id && record.teacherId == Auth.auth().currentUser?.uid && Calendar.current.isDate(record.date, inSameDayAs: attendanceViewModel.selectedDate)
                    }
                    if let recordToDelete = recordToDelete {
                        attendanceViewModel.deleteAttendance(attendanceRecord: recordToDelete)
                    }
                }
                attendanceViewModel.loadData()
            }
        }
    }
}

struct AttendanceRecord: Identifiable, Codable {
    @DocumentID var id: String?
    var childId: String
    var date: Date
    var teacherId: String
    var teacherName: String
    var isPresent: Bool = true
    var notes: String?
}

class ChildViewModel: ObservableObject {
    @Published var children: [Child] = []
    @Published var errorMessage: String? = nil

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        listenerRegistration = db.collection("children")
            .order(by: "name")
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting children: \(error.localizedDescription)"
                    print("Error getting children: \(error)")
                    return
                }

                self.children = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: Child.self)
                } ?? []
            }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addChild(child: Child) {
        do {
            _ = try db.collection("children").addDocument(from: child)
        } catch {
            errorMessage = "Error adding child: \(error.localizedDescription)"
            print("Error adding child: \(error)")
        }
    }

    func updateChild(child: Child) {
        guard let childId = child.id else { return }
        do {
            try db.collection("children").document(childId).setData(from: child)
        } catch {
            errorMessage = "Error updating child: \(error.localizedDescription)"
            print("Error updating child: \(error)")
        }
    }

    func deleteChild(child: Child) {
        guard let childId = child.id else { return }
        db.collection("children").document(childId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting child: \(error.localizedDescription)"
                print("Error deleting child: \(error)")
            }
        }
    }
}

class AttendanceViewModel: ObservableObject {
    @Published var attendanceRecords: [AttendanceRecord] = []
    @Published var errorMessage: String? = nil
    @Published var selectedDate: Date = Date() // Date of the attendance record

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    //Load Data based on the day
    func loadData() {
        guard let teacherId = Auth.auth().currentUser?.uid else {
            errorMessage = "Not signed in"
            return
        }

        // Get the start and end of the selected date
        let calendar = Calendar.current
        let startDate = calendar.startOfDay(for: selectedDate)
        let endDate = calendar.date(byAdding: .day, value: 1, to: startDate)!

        listenerRegistration = db.collection("attendance")
            .whereField("teacherId", isEqualTo: teacherId)
            .whereField("date", isGreaterThanOrEqualTo: startDate)
            .whereField("date", isLessThan: endDate)
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting attendance records: \(error.localizedDescription)"
                    print("Error getting attendance records: \(error)")
                    return
                }

                self.attendanceRecords = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: AttendanceRecord.self)
                } ?? []
            }
    }

    deinit {
        listenerRegistration?.remove()
    }

    //Update the record Attendance function
    func recordAttendance(childId: String, isPresent: Bool, notes: String?, teacherName: String) {
        guard let teacherId = Auth.auth().currentUser?.uid else {
            errorMessage = "Not signed in"
            return
        }
        let newAttendance = AttendanceRecord(childId: childId, date: selectedDate, teacherId: teacherId, teacherName: teacherName, isPresent: isPresent, notes: notes)
        do {
            _ = try db.collection("attendance").addDocument(from: newAttendance)
            loadData()  // Refresh the data
        } catch {
            errorMessage = "Error recording attendance: \(error.localizedDescription)"
            print("Error recording attendance: \(error)")
        }
    }

    // New function to delete attendance records from Firebase
    func deleteAttendance(attendanceRecord: AttendanceRecord) {
        guard let attendanceId = attendanceRecord.id else { return }
        db.collection("attendance").document(attendanceId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting attendance: \(error.localizedDescription)"
                print("Error deleting attendance: \(error)")
            } else {
                self.loadData() // Refresh the data after successful deletion
            }
        }
    }

    func updateAttendance(attendanceRecord: AttendanceRecord) {
        guard let attendanceId = attendanceRecord.id else { return }
        do {
            try db.collection("attendance").document(attendanceId).setData(from: attendanceRecord)
        } catch {
            errorMessage = "Error updating attendance: \(error.localizedDescription)"
            print("Error updating attendance: \(error)")
        }
    }
}

struct Lesson: Identifiable, Codable {
    @DocumentID var id: String?
    var title: String
    var description: String
    var teacherId: String
    var teacherName: String // Added for display purposes
    var date: Date = Date()
    var category: LessonCategory //ADDED LESSON CATEGORY
}

enum LessonCategory: String, Codable, CaseIterable {
    case bibleStudy = "Bible Study"
    case prayer = "Prayer"
    case activity = "Activity"
    case other = "Other"

    var displayName: String {
        return self.rawValue
    }
}

class LessonViewModel: ObservableObject {
    @Published var lessons: [Lesson] = []
    @Published var errorMessage: String? = nil

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        listenerRegistration = db.collection("lessons")
            .order(by: "date", descending: true)
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting lessons: \(error.localizedDescription)"
                    print("Error getting lessons: \(error)")
                    return
                }

                self.lessons = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: Lesson.self)
                } ?? []
            }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addLesson(title: String, description: String, teacherName: String, category: LessonCategory) {
        guard let userId = Auth.auth().currentUser?.uid else {
            errorMessage = "Not signed in"
            return
        }

        let newLesson = Lesson(title: title, description: description, teacherId: userId, teacherName: teacherName, category: category)
        do {
            _ = try db.collection("lessons").addDocument(from: newLesson)
        } catch {
            errorMessage = "Error adding lesson: \(error.localizedDescription)"
            print("Error adding lesson: \(error)")
        }
    }

    func updateLesson(lesson: Lesson) {
        guard let lessonId = lesson.id else { return }
        do {
            try db.collection("lessons").document(lessonId).setData(from: lesson)
        } catch {
            errorMessage = "Error updating lesson: \(error.localizedDescription)"
            print("Error updating lesson: \(error)")
        }
    }

    func deleteLesson(lesson: Lesson) {
        guard let lessonId = lesson.id else { return }
        db.collection("lessons").document(lessonId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting lesson: \(error.localizedDescription)"
                print("Error deleting lesson: \(error)")
            }
        }
    }
}

struct AddLessonView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var lessonViewModel: LessonViewModel
    @State private var title = ""
    @State private var description = ""
    @State private var category: LessonCategory = .bibleStudy

    var body: some View {
        NavigationView {
            Form {
                TextField("Title", text: $title)
                TextEditor(text: $description)
                    .frame(height: 200)

                Picker("Category", selection: $category) {
                    ForEach(LessonCategory.allCases, id: \.self) { category in
                        Text(category.displayName).tag(category)
                    }
                }
                .pickerStyle(.menu)

                Button("Add Lesson") {
                    //Access the username from the authService for add Lesson
                    guard let name = authService.user?.email else {return}
                    lessonViewModel.addLesson(title: title, description: description, teacherName: name, category: category)
                    dismiss()
                }
            }
            .navigationTitle("Add Lesson")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct LessonListView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var lessonViewModel: LessonViewModel
    @State private var showingAddLesson = false

    var body: some View {
        NavigationView {
            List {
                ForEach(lessonViewModel.lessons) { lesson in
                    NavigationLink(destination: LessonDetailView(lesson: lesson)) {
                        LessonRow(lesson: lesson)
                            .frame(height: 100)
                    }
                }
                .onDelete(perform: deleteLesson)
            }
            .navigationTitle("Lessons")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    //Check for admin/childrenMinistry permission
                    if authService.staffCategory == .childrenMinistry || authService.userHasAdminRights() {
                        Button {
                            showingAddLesson = true
                        } label: {
                            Image(systemName: "plus")
                        }
                    }
                }
            }
            .sheet(isPresented: $showingAddLesson) {
                AddLessonView(lessonViewModel: lessonViewModel)
                    .environmentObject(authService)
            }
            .onAppear {
                lessonViewModel.loadData()
            }
        }
    }

    func deleteLesson(indexSet: IndexSet) {
        //Implement the Delete functions
        guard let index = indexSet.first else { return }
        let lessonToDelete = lessonViewModel.lessons[index]
        lessonViewModel.deleteLesson(lesson: lessonToDelete)
    }
}

struct LessonRow: View {
    let lesson: Lesson

    var body: some View {
        VStack(alignment: .leading) {
            Text(lesson.title)
                .font(.headline)
            Text(lesson.teacherName)
                .font(.subheadline)
                .foregroundColor(.secondary)
        }
    }
}

struct LessonDetailView: View {
    let lesson: Lesson

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 15) {
                Text(lesson.title)
                    .font(.largeTitle)
                    .fontWeight(.bold)

                Text("Teacher: \(lesson.teacherName)")
                    .font(.headline)
                    .foregroundColor(.secondary)

                Text("Category: \(lesson.category.displayName)")
                                    .font(.headline)
                                    .foregroundColor(.secondary)

                Divider()

                Text("Description:")
                    .font(.title2)
                Text(lesson.description)
                    .font(.body)

                Spacer()
            }
            .padding()
        }
        .navigationTitle("Lesson Details")
    }
}

struct Book: Identifiable, Codable {
    @DocumentID var id: String?
    var title: String
    var uploadedBy: String
    var uploadedById: String // User ID of uploader
    var isApproved: Bool = false
    var approvedBy: String?  // Name of approver
    var category: BookCategory //New addition
    var bookCompleted: Bool = false //Checkmark to state if the book is approved or not

    enum CodingKeys: String, CodingKey {
        case id
        case title
        case uploadedBy
        case uploadedById
        case isApproved
        case approvedBy
        case category
        case bookCompleted

    }
}

enum BookCategory: String, Codable, CaseIterable {
    case fiction = "Fiction"
    case nonFiction = "Non-Fiction"
    case inspirational = "Inspirational"
    case childrens = "Children's"
    case other = "Other"

    var displayName: String {
        return self.rawValue
    }
}

struct Page: Identifiable, Codable {
    @DocumentID var id: String?
    var bookId: String
    var pageNumber: Int // Order pages
    var text: String
}

class BookViewModel: ObservableObject {
    @Published var books: [Book] = []
    @Published var errorMessage: String? = nil
    @Published var showAllBooks = false  //Toggle to check the books
    @Published var bookCompleted = false

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        var query: Query = db.collection("books")
            .order(by: "title")

        if !showAllBooks {
            query = query.whereField("isApproved", isEqualTo: true)
        }

        listenerRegistration = query.addSnapshotListener { (querySnapshot, error) in
            if let error = error {
                self.errorMessage = "Error getting books: \(error.localizedDescription)"
                print("Error getting books: \(error)")
                return
            }

            self.books = querySnapshot?.documents.compactMap { document in
                try? document.data(as: Book.self)
            } ?? []
        }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addBook(title: String, uploadedBy: String, uploadedById: String, category: BookCategory) {
        let newBook = Book(title: title, uploadedBy: uploadedBy, uploadedById: uploadedById, category: category)
        do {
            _ = try db.collection("books").addDocument(from: newBook)
        } catch {
            errorMessage = "Error adding book: \(error.localizedDescription)"
            print("Error adding book: \(error)")
        }
    }

    func updateBook(book: Book) {
        guard let bookId = book.id else { return }
        do {
            try db.collection("books").document(bookId).setData(from: book)
        } catch {
            errorMessage = "Error updating book: \(error.localizedDescription)"
            print("Error updating book: \(error)")
        }
    }

    func deleteBook(book: Book) {
        guard let bookId = book.id else { return }
        db.collection("books").document(bookId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting book: \(error.localizedDescription)"
                print("Error deleting book: \(error)")
            }
        }
    }
}

class PageViewModel: ObservableObject {
    @Published var pages: [Page] = []
    @Published var errorMessage: String? = nil

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init(bookId: String) {
        loadData(bookId: bookId)
    }

    func loadData(bookId: String) {
        listenerRegistration = db.collection("books").document(bookId).collection("pages")
            .order(by: "pageNumber")
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting pages: \(error.localizedDescription)"
                    print("Error getting pages: \(error)")
                    return
                }

                self.pages = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: Page.self)
                } ?? []
            }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addPage(bookId: String, text: String) {
        // Determine the next available page number
        let newPageNumber = (pages.last?.pageNumber ?? 0) + 1
        let newPage = Page(bookId: bookId, pageNumber: newPageNumber, text: text)

        do {
            _ = try db.collection("books").document(bookId).collection("pages").addDocument(from: newPage)
        } catch {
            errorMessage = "Error adding page: \(error.localizedDescription)"
            print("Error adding page: \(error)")
        }
    }

    func updatePage(page: Page) {
        guard let pageId = page.id else { return }
        do {
            try db.collection("books").document(page.bookId).collection("pages").document(pageId).setData(from: page)
        } catch {
            errorMessage = "Error updating page: \(error.localizedDescription)"
            print("Error updating page: \(error)")
        }
    }

    func deletePage(page: Page) {
        guard let pageId = page.id else { return }
        db.collection("books").document(page.bookId).collection("pages").document(pageId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting page: \(error.localizedDescription)"
                print("Error deleting page: \(error)")
            }
        }
    }
}

struct BookListView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var bookViewModel = BookViewModel()
    @State private var showingAddBook = false
    
    var body: some View {
        NavigationView {
            List {
                ForEach(bookViewModel.books) { book in
                    NavigationLink(destination: BookDetailView(book: book)) {
                        BookRow(book: book)
                            .frame(height: 100)
                    }
                }
                .onDelete(perform: deleteBook)
            }
            .navigationTitle("Books")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    if authService.isLoggedIn {
                        Button {
                            showingAddBook = true
                        } label: {
                            Image(systemName: "plus")
                        }
                    }
                }
            }
            .sheet(isPresented: $showingAddBook) {
                AddBookView(bookViewModel: bookViewModel)
                    .environmentObject(authService)
            }
            .onAppear {
                bookViewModel.loadData()
            }
        }
    }
    
    func deleteBook(indexSet: IndexSet) {
        //Implement the Delete functions
        guard let index = indexSet.first else { return }
        let lessonToDelete = bookViewModel.books[index]
        bookViewModel.deleteBook(book: lessonToDelete)
    }
}

struct BookRow: View {
    let book: Book

    var body: some View {
        VStack(alignment: .leading) {
            Text(book.title)
                .font(.headline)
            Text(book.uploadedBy)
                .font(.subheadline)
                .foregroundColor(.secondary)
        }
    }
}

struct AddBookView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var bookViewModel: BookViewModel
    @State private var title = ""
    @State private var bookCompleted: Bool = false
    @State private var category: BookCategory = .fiction

    var body: some View {
        NavigationView {
            Form {
                TextField("Title", text: $title)

                Picker("Category", selection: $category) {
                    ForEach(BookCategory.allCases, id: \.self) { category in
                        Text(category.displayName).tag(category)
                    }
                }
                .pickerStyle(.menu)

                Button("Add Book") {
                    guard let userId = Auth.auth().currentUser?.uid, let userName = Auth.auth().currentUser?.email else { return }
                    bookViewModel.addBook(title: title, uploadedBy: userName, uploadedById: userId, category: category)
                    dismiss()
                }
            }
            .navigationTitle("Add Book")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct BookDetailView: View {
    @EnvironmentObject var authService: AuthenticationService
    let book: Book
    @StateObject var pageViewModel : PageViewModel

    init(book: Book) {
            self.book = book
            _pageViewModel = StateObject(wrappedValue: PageViewModel(bookId: book.id ?? ""))
    }

    @State private var showingAddPageView = false

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 15) {
                Text(book.title)
                    .font(.largeTitle)
                    .fontWeight(.bold)

                Text("Uploaded by: \(book.uploadedBy)")
                    .font(.headline)
                    .foregroundColor(.secondary)

                Button {
                    showingAddPageView = true
                } label: {
                    Text("Add Page")
                        .padding()
                        .background(Color.blue)
                        .foregroundColor(.white)
                        .cornerRadius(8)
                }

                Spacer()

                NavigationLink(destination: PageReaderView(book: book)) {
                    Text("Read Book")
                        .padding()
                        .background(Color.blue)
                        .foregroundColor(.white)
                        .cornerRadius(8)
                }

                .sheet(isPresented: $showingAddPageView) {
                    PageAddView(pageViewModel: pageViewModel, book: book)
                        .environmentObject(authService)

                }
            }
            .padding()
        }
        .navigationTitle("Book Details")
    }
}

struct PageReaderView: View {
    @EnvironmentObject var authService: AuthenticationService
    @StateObject var pageViewModel: PageViewModel
    let book: Book

    @State private var currentPageNumber: Int = 1
    @State private var currentPage: Page? = nil  // State for currently displayed page

    init(book: Book) {
        self.book = book
        _pageViewModel = StateObject(wrappedValue: PageViewModel(bookId: book.id ?? ""))
    }

    var body: some View {
        VStack {
            // Display PageNumber
            Text("Page \(currentPageNumber) / \(pageViewModel.pages.count)")
                .font(.headline)
                .padding(.top)

            // Display page content
            ScrollView {
                Text(currentPage?.text ?? "No content available")
                    .padding()
            }

            HStack {
                // Previous Page
                Button(action: {
                    if currentPageNumber > 1 {
                        currentPageNumber -= 1
                        loadCurrentPage()
                    }
                }) {
                    Image(systemName: "arrow.left.circle.fill")
                        .font(.title)
                        .disabled(currentPageNumber == 1)
                }
                .disabled(currentPageNumber == 1)

                Spacer()

                // Next Page
                Button(action: {
                    if currentPageNumber < pageViewModel.pages.count {
                        currentPageNumber += 1
                        loadCurrentPage()
                    }
                }) {
                    Image(systemName: "arrow.right.circle.fill")
                        .font(.title)
                        .disabled(currentPageNumber == pageViewModel.pages.count)
                }
                .disabled(currentPageNumber == pageViewModel.pages.count)
            }
            .padding()
        }
        .onAppear {
            loadCurrentPage()
        }
    }

    // Load current page based on page number
    func loadCurrentPage() {
        currentPage = pageViewModel.pages.first { $0.pageNumber == currentPageNumber }
    }
}

struct PageAddView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @StateObject var pageViewModel: PageViewModel

    @State private var text: String = ""
    let book: Book

    var body: some View {
        NavigationView {
            Form {
                Section(header: Text("Page Content (Max 1000 characters)")) {
                    TextEditor(text: $text)
                        .frame(height: 200)
                        .onChange(of: text) { newValue in
                            if newValue.count > 1000 {
                                text = String(newValue.prefix(1000))
                            }
                        }
                }
                Text("\(text.count) / 1000 Characters")

                Button("Add Page") {
                    pageViewModel.addPage(bookId: book.id ?? "", text: text)
                    dismiss()
                }
            }
            .navigationTitle("Add Page")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
}

struct AppUser: Identifiable, Codable {  // Changed User to AppUser
    @DocumentID var id: String?  // Firestore document ID (same as Firebase Auth UID)
    var firstName: String
    var lastName: String
    var birthday: Date
    var phoneNumber: String
    var email: String
    var city: String
    var country: String
    var membershipType: MembershipType // Full Time, Part Time, or Other (Enum)
    var staffCategory: StaffCategory? = nil  // Optional Staff Category
    var imageUrl: String? = nil //ADD IMG URL

    // Enum for Membership Type
    enum MembershipType: String, Codable, CaseIterable {
        case fullTime = "Full Time"
        case partTime = "Part Time"
        case other = "Other"

        var displayName: String {
            return self.rawValue
        }
    }
}

// (Post Model and GradientText structs remain the same)

struct ChurchUserPostCardView: View {
    ///for live updates
    @State private var docListener: ListenerRegistration?
    @EnvironmentObject var authService: AuthenticationService
    @State var userAvatarURL: URL? = nil
    @State private var isLiked = false
    var post: Post
    ///callbacks
    var onUpdate: (Post) -> ()
    var onDelete: () -> ()

    var body: some View {
        HStack(alignment: .top, spacing: .medium) {
            if userAvatarURL != nil {
                GeometryReader{ geometry in
                    let size = geometry.size
                    WebImage(url: userAvatarURL!)
                        .resizable()
                        .aspectRatio(contentMode: .fill)
                        .frame(width: size.width, height: size.height)
                        .padding(.top, .large)
                        .clipShape(RoundedRectangle(cornerRadius: 10, style: .continuous))
                }
                .frame(width: 35, height: 35)
            } else {
                Image(uiImage: UIImage.init(named: "Logo Apple")!)
                    .resizable()
                    .aspectRatio(contentMode: .fill)
                    .frame(width: 35, height: 35)
                    .cornerRadius(10)
                    .padding(.top, .large)
            }

            VStack(alignment: .leading, spacing: .small) {
                if #available(iOS 16.0, *) {
                    GradientText(text: post.userName)
                        .font(.callout)
                        .fontWeight(.semibold)
                        .glow(color: .blue.opacity(0.2))
                } else {
                    // Fallback on earlier versions
                    GradientText(text: post.userName)
                        .font(.callout)
                        .glow(color: .blue.opacity(0.2))
                }
                
                Text(post.publishedDate.formatted(date: .numeric, time: .shortened)) //add condition check to make this .standard if one week elapses
                    .font(.caption2)
                    .foregroundColor(.white)
                
                Text(post.text)
                    .foregroundColor(.white)
                    .textSelection(.enabled)
                    .shadow(radius: 2)
                    .padding(.vertical, .medium)
                    .padding(.horizontal, .small)

                if let postImageURL = post.imageURL {
                    GeometryReader{ geometry in
                        let size = geometry.size
                        WebImage(url: postImageURL)
                            .resizable()
                            .aspectRatio(contentMode: .fill)
                            .frame(width: size.width, height: size.height)
                            .clipShape(RoundedRectangle(cornerRadius: 10, style: .continuous))
                    }
                    .frame(height: 200)
                }

                InternalPostInteraction()
            }
        }
        .task {
            do {
                //Replace the userVM object with the AppUser object
                self.userAvatarURL = try await getAvatarURL(for: post.userUID) //Replace the function name
            } catch {
                print("Error fetching avatar URL: \(error)")
            }
        }
        .horizontalAlign(.leading)
        .overlay(alignment: .topTrailing, content: {
            //delete button for OP
            if let user_id = authService.user?.id { //Replace the check with AppUser
                if post.userUID == user_id {
                    Menu {
                        Button(role: .destructive) {
                            deletePost()
                        } label: {
                            Text("Delete This Post")
                        }

                    } label: {
                        Image(systemName: "ellipsis")
                            .font(.caption)
                            .rotationEffect(.init(degrees: -90))
                            .foregroundColor(.white)
                            .padding(.medium)
                            .contentShape(Rectangle())
                    }
                    .offset(x: .medium)
                }
            }
        })
        .onAppear {
            //docListener is added only when post is visible onscreen
            //LazyVStack is designed to emit notifications if a child enters or exits screen visibility through onAppear & onDisappear methods respectively
            ///adding only once
            if docListener == nil {
                guard let postID = post.id else {
                    //setErrorWithMessage("Error", "Post ID not saved and initialised properly", handler: {})
                    authService.errorMessage = "Post ID not saved and initialised properly"
                    return
                }
                docListener = Firestore.firestore().collection("Posts").document(postID).addSnapshotListener({ snapshot, error in
                    if snapshot != nil {
                        if snapshot!.exists { //iffy
                            /// - Document updated
                            /// fetching updated document
                            if let updatedPost = try? snapshot!.data(as: Post.self) {
                                onUpdate(updatedPost)
                            }
                        } else {
                           /// - Document deleted
                            onDelete()
                        }
                    }
                })
            }
        }
        .onDisappear {
            //Ensuring that live updates only occur for documents visible on screen to lower I/O cost of Firebase reads
            // DocumentListener is removed in such instances
            if docListener != nil {
                docListener!.remove()
                self.docListener = nil
            }
        }
    }

    //upvote/downvote
    @ViewBuilder
    func InternalPostInteraction() -> some View {
        HStack(spacing: .small) {
            Button {
                upvote()
                if !isLiked {
                    isLiked = true
                }
            } label: {
                //Replace userVM.userID object check with the authService.id property
                if let userID = authService.user?.id { //Replace the userVM
                    ZStack {
                        Image(systemName: post.upvoteIDs.contains(userID) ? "star.fill" : "star")

                        Circle()
                            .strokeBorder(lineWidth: isLiked ? 5 : 0)  // Reduced lineWidth for less height impact
                            .frame(width: 40, height: 40)  // Adjusted frame to reduce size
                            .foregroundColor(Color(.systemPink))
                            .hueRotation(.degrees(isLiked ? 200 : 300))
                            .scaleEffect(isLiked ? 0 : 1.3)
                            .animation(.easeInOut(duration: 0.5), value: isLiked)

                        SplashView()
                            .opacity(isLiked ? 1 : 0)
                            .scaleEffect(isLiked ? 0 : 1.3)  // Reduce scale to avoid adding height
                            .animation(.easeInOut(duration: 0.5), value: isLiked)

                        SplashView()
                            .rotationEffect(.degrees(90))
                            .opacity(isLiked ? 1 : 0)
                            .offset(y: isLiked ? -4 : 4)  // Reduced offset
                            .scaleEffect(isLiked ? 0 : 1.3)
                            .animation(.easeInOut(duration: 0.5), value: isLiked)
                    }
                } else {
                    Image(systemName: "star")
                }
            }

            Text("\(post.upvoteIDs.count)")
                .font(.caption)
                .foregroundColor(.green)
                .glow(color: .green, radius: 1)

            Button {
                downVote()
            } label: {
                if let userID = authService.user?.id {
                    Image(systemName: post.downvoteIDs.contains(userID) ? "hand.thumbsdown.fill" : "hand.thumbsdown")
                } else {
                    Image(systemName: "hand.thumbsdown")
                }
            }
            .padding(.leading, .xLarge)

            Text("\(post.downvoteIDs.count)")
                .font(.caption)
                .foregroundColor(.red)
        }
        .foregroundColor(.white)
        .padding(.vertical, .small)  // Reduced vertical padding
        .padding(.horizontal, .small)
        .padding(.trailing, .medium)
        .background(
            RoundedRectangle(cornerRadius: 10)
                .fill(.clear)
                .blurBackground()
        )
    }

    func upvote() {
        Task {
            guard let postID = post.id else {
                //await setErrorWithMessage("Error", "Internal-Post ID not saved and initialised properly", handler: {})
                authService.errorMessage = "Internal-Post ID not saved and initialised properly"
                return
            }
            //Replace userVM.userProfile for the AppUser Object, as we check the details for every authetnicatd User on login
            guard let userUID = authService.user?.id else { //Here also replace the userID with the authservice one
                //await setErrorWithMessage("Error", "User ID not found", handler: {})
                authService.errorMessage = "User ID not found"
                return
            }
            do {
                if post.upvoteIDs.contains(userUID) {
                    // Remove upvote if already upvoted
                    try await Firestore.firestore().collection("Posts").document(postID).updateData([
                        "upvoteIDs" : FieldValue.arrayRemove([userUID])
                    ])
                } else {
                    // Add upvote and remove downvote if exists
                    try await Firestore.firestore().collection("Posts").document(postID).updateData([
                        "upvoteIDs" : FieldValue.arrayUnion([userUID]),
                        "downvoteIDs" : FieldValue.arrayRemove([userUID])
                    ])
                }
                isLiked = post.upvoteIDs.contains(userUID)
            } catch {
                //await setError(error)
                authService.errorMessage = error.localizedDescription
            }
        }
    }

    func downVote() {
        Task {
            guard let postID = post.id else {
                //await setErrorWithMessage("Error", "Internal-Post ID not saved and initialised properly", handler: {})
                authService.errorMessage = "Internal-Post ID not saved and initialised properly"
                return
            }
            //Replace userVM.userProfile for the AppUser Object, as we check the details for every authetnicatd User on login
            guard let userUID = authService.user?.id else {
                //await setErrorWithMessage("Error", "User ID not found", handler: {})
                authService.errorMessage = "User ID not found"
                return
            }
            do {
                if post.downvoteIDs.contains(userUID) {
                    // Remove downvote if already downvoted
                    try await Firestore.firestore().collection("Posts").document(postID).updateData([
                        "downvoteIDs" : FieldValue.arrayRemove([userUID])
                    ])
                } else {
                    // Add downvote and remove upvote if exists
                    try await Firestore.firestore().collection("Posts").document(postID).updateData([
                        "upvoteIDs" : FieldValue.arrayRemove([userUID]),
                        "downvoteIDs" : FieldValue.arrayUnion([userUID])
                    ])
                }
                isLiked = false
            } catch {
               // await setError(error)
                authService.errorMessage = error.localizedDescription
            }
        }
    }

    func deletePost() {
        Task {
            //step 1 - delete image from firebase storage
            do {
                guard let userUID = authService.user?.id else { //User the new AppUser object to verify the AppUser ID
                    //Task {
                     //   await setErrorWithMessage("Error", "Login Initialisation Error", handler: {})
                    //}
                    authService.errorMessage = "Login Initialisation Error"
                    return
                }
                if post.imageReferenceID != "" {
                    try await Storage.storage().reference().child(userUID).child("Social_Images").child(post.imageReferenceID).delete()
                }
                //Step 2 - delete firestore document
                guard let postID = post.id else {
                    //Task {
                    //    await setErrorWithMessage("Error", "Something went wrong with initalising this missive", handler: {})
                    //}
                    authService.errorMessage = "Something went wrong with initalising this missive"
                    return
                }
                try await Firestore.firestore().collection("Posts").document(postID).delete()
            } catch {
                //await setError(error)
                authService.errorMessage = error.localizedDescription
            }
        }
    }

    //replace all of these with AuthService functions, to follow dependency inversion/single resopnsibility principle
    func getAvatarURL(for userUID: String) async throws -> URL? {
       //we already have the staff members from the first fetch, so can try this as well.

            guard let imageURL = authService.user?.imageUrl else {
                return nil
            }

            return URL(string: imageURL)
    }

    func setErrorWithMessage(_ title : String, _ message: String, handler: @escaping () -> Void) async {
        self.authService.errorMessage = message
    }

    func setError(_ error: Error) async {
        self.authService.errorMessage = error.localizedDescription
    }
}

struct ChurchNewPost: View {
    @EnvironmentObject var authService: AuthenticationService

    /// callback
    var onPost : (Post) -> ()

    @Environment(\.dismiss) var dismiss

    @State private var isLoading: Bool = false
    @State private var isShowingPhotoPicker: Bool = false
    @State private var avatar: UIImage? = nil
    @State private var postText: String = "" //Moving posttext to this page
    @State private var postImageData: Data? = nil //Move this to the newPost
    @FocusState private var showKeyboard : Bool //to toggle keyboard onscreen

    var body: some View {
        VStack{
            HStack {
                Menu {
                    Button(role: .destructive) {
                        dismiss()
                    } label: {
                        Text("Cancel")
                    }
                } label: {
                    Text("Cancel")
                        .font(.callout)
                        .foregroundColor(.black)
                }
                .horizontalAlign(.leading)

                Button {
                    isLoading = true
                    showKeyboard = false
                    createPost(onPost: onPost, postImageData: postImageData)
                    isLoading = false
                    dismiss()
                } label: {
                    Text("Post")
                        .font(.callout)
                        .foregroundColor(.white)
                        .padding(.horizontal, .large)
                        .padding(.vertical, .medium)
                        .background(.pink, in: Capsule())
                }
                .disableWithOpacity(postText == "")
            }
            .padding(.horizontal, .large)
            .padding(.vertical, .medium)
            .background (
                Rectangle()
                    .fill(.gray.opacity(0.05))
                    .ignoresSafeArea()
            )

            ScrollView(.vertical, showsIndicators: false) {
                VStack {
                    if #available(iOS 16.0, *) {
                        TextField("What's New", text: $postText, axis: .vertical)
                            .focused($showKeyboard)
                            .padding(postText.isEmpty ? .xLarge : 0)
                            .paddedBorder(.white, 1)
                    } else {
                        // Fallback on earlier versions
                     TextEditor(text: $postText)
                         .focused($showKeyboard)
                         .padding(postText.isEmpty ? 16 : 0) // Using a numeric value instead of .xLarge
                         .overlay(
                             RoundedRectangle(cornerRadius: 8)
                                 .stroke(Color.white, lineWidth: 1)
                         )
                         .overlay(
                             Group {
                                 if postText.isEmpty {
                                     Text("What's New")
                                         .foregroundColor(.gray)
                                         .padding(.leading, 20)
                                         .padding(.top, 8)
                                 }
                             },
                             alignment: .topLeading
                         )
                    }


                    if let postImageData = postImageData, let image = UIImage(data: postImageData) {
                        GeometryReader{ geometry in
                            let size = geometry.size
                            Image(uiImage: image)
                                .resizedToFill(width: size.width, height: size.height)
                                .clipShape(RoundedRectangle(cornerRadius: 10, style: .continuous))
                                .overlay(alignment: .topTrailing) {
                                    //remove image button
                                    Button {
                                        withAnimation(.easeInOut(duration: 0.3)) {
                                            self.postImageData = nil //iffy. may need binding for this
                                        }
                                    } label: {
                                        Image(systemName: "trash")
                                            .tint(.red)
                                    }
                                    .padding(.medium)
                                    //should add background here to make this more visible
                                }
                        }
                        .clipped()
                        .frame(height: 220)
                    }
                }
                .padding(.large)
            }

            Divider()

            HStack {
                Button {
                    isShowingPhotoPicker.toggle()
                } label: {
                    Image(systemName: "photo.on.rectangle")
                        .font(.title3)
                }
                .horizontalAlign(.leading)

                Button {
                    showKeyboard = false
                } label: {
                    Text("Hide Keyboard")
                }
            }
            .foregroundColor(.white)
            .padding(.horizontal, .large)
            .padding(.vertical, 10)
        }
        .verticalAlign(.top)
        .sheet(isPresented: $isShowingPhotoPicker) {
            isShowingPhotoPicker = false
        } content: {
            PhotoPicker(avatar: $avatar)
        }
        .onChange(of: avatar) { newValue in
            if newValue != nil {
                Task {
                    if let data = newValue!.jpegData(compressionQuality: 0.2) {
                        //UI must be updated on Main Thread
                        //iOS expects all UI changes to be on the main thread. SwiftUI is no exception
                        await MainActor.run(body: {
                            self.postImageData = data
                            avatar = nil
                        })
                    }
                }
            }
        }
        .overlay {
            LoadingView(show: $isLoading)
        }
    }

    //Pass the postImageData to CreatePost
    func createPost(onPost: @escaping (Post) -> (), postImageData: Data?) {
        Task {
            do {
                guard let user = authService.user else {
                    //await setErrorWithMessage("Error", "Internal user login status error", handler: {})
                    authService.errorMessage = "Internal user login status error"
                    return
                }
                guard let profileID = Auth.auth().currentUser?.uid else {
                    //await setErrorWithMessage("Error", "Internal user login status error", handler: {})
                    authService.errorMessage = "Internal user login status error"
                    return
                }
                guard !Auth.auth().currentUser!.isAnonymous else {
                    //await setErrorWithMessage("Error", "Unable to post content for private user |Anonymous User|", handler: {})
                    authService.errorMessage = "Unable to post content for private user |Anonymous User|"
                    return
                }
                let userName = "\(user.firstName) \(user.lastName)" //Now setting the username as a combo of these

                //uploading image, if any
                let imageReferenceID = "\(profileID)\(Date())"
                let storageRef = Storage.storage().reference().child(profileID).child("Social_Images").child(imageReferenceID)
                if postImageData != nil {
                    let _ = try await storageRef.putDataAsync(postImageData!)
                    let downloadURL = try await storageRef.downloadURL()

                    //creating post image
                    let post = Post(text: postText, imageURL: downloadURL, imageReferenceID: imageReferenceID, userName: userName , userUID: profileID)
                    try await createDocumentInFirebase(post, onPost)
                } else {
                    //Save plain post with no image provided
                    let post = Post(text: postText, userName: userName, userUID: profileID)
                    try await createDocumentInFirebase(post, onPost)
                }
            } catch {
                //await setError(error)
                authService.errorMessage = error.localizedDescription
            }
        }
    }

    func createDocumentInFirebase(_ post : Post, _ onPost: @escaping (Post) -> ()) async throws {
        //writing document to firebase firestore
        let doc = Firestore.firestore().collection("Posts").document()
        let _ = try doc.setData(from: post, completion: { error in
            if error == nil {
                var updatedPost = post
                updatedPost.id = doc.documentID
                onPost(updatedPost)
                //showSuccessAlertView("✔️", "Success", handler: {})

            } else {
                //Task {
                  //  await setErrorWithMessage("Error", error!.localizedDescription, handler: { return })
               // }
                self.authService.errorMessage = error?.localizedDescription
            }
        })
    }
}

/// To eliminate redundant code since we have to display the current user's posts on screen and display another user's posts when searching for that user
struct ReusableChurchPostsView: View {
    @EnvironmentObject var authService: AuthenticationService
    @State var isFetching: Bool = false
    @Binding var posts : [Post]
    ///Pagination
    @State private var paginationDoc: QueryDocumentSnapshot?
    
    var body: some View {
        ScrollView(.vertical, showsIndicators: false) {
            LazyVStack { //LazyVStack allows us to receive notifications via onAppear and onDisappear when child views move outside the screen
                if isFetching {
                    ProgressView()
                        .padding(.top, .xxLarge)
                } else {
                    if posts.isEmpty {
                        GradientText(text: "Share Your Light. This is the place for testimonies, prayer requests, and words of encouragement. Create your first post now")
                            .padding(.top, .xxLarge)
                    } else {
                        Posts()
                    }
                }
            }
            .padding(.large)
        }
        .refreshable {
            //refreshing for posts fetched by UID not supported
            //guard !basedOnUID else { return }
            isFetching = true
            posts = []
            //resetting pagination doc for refreshes since refresh will fetch most recent posts, hence paginationdoc needs to be updated
            paginationDoc = nil
            await fetchPosts()
        }
        .task {
            //running fetch operation only when internalPosts has no values
            guard posts.isEmpty else  { return }
            await fetchPosts()
        }
    }
    
    /// Displayiing fetched posts
    @ViewBuilder func Posts() -> some View {
        ForEach(posts) { post in
            ChurchUserPostCardView(post: post) { updatedPost in
                //updating post in array
                if let index = posts.firstIndex(where: { post in
                    post.id == updatedPost.id
                }) {
                    posts[index].upvoteIDs = updatedPost.upvoteIDs
                    posts[index].downvoteIDs = updatedPost.downvoteIDs
                }
            } onDelete: {
                //removing post from array
                withAnimation(.easeInOut(duration: 0.25)) {
                    posts.removeAll {
                        post.id == $0.id
                    }
                }
            }
            .onAppear {
                /// we null-check pagination here as well, so that when each batch fetch is completed, we will no longer run fetch operation when last document has been reached. This is an infinite scroll feature, which will not overwhelm our db capabilities with I/O operations. Fetch operations run only after user scrolls to the end of previous batch
                if post.id == posts.last?.id && paginationDoc != nil {
                    Task {
                        await fetchPosts()
                    }
                }
            }

            Divider()
                .padding(.horizontal, -15)
                .foregroundColor(.white)
        }
    }
    
    //should find a way to use this while following MVVM pattern
    
    /// asynchronously fetching posts
    /// will run fetch operation for recent posts, or for a specific userUID based on the Boolean value of basedOnUID
    func fetchPosts() async {
        do {
            var query: Query!
            ///implementing pagination
            if paginationDoc != nil {
                query = Firestore.firestore().collection("Posts")
                    .order(by: "publishedDate", descending: true)
                    .start(afterDocument: paginationDoc!)
                    .limit(to: 20)
            } else {
                query = Firestore.firestore().collection("Posts")
                    .order(by: "publishedDate", descending: true)
                    .limit(to: 20)
            }
            
            // new query for UID based fetch operation, by filtering out posts not attached to the passed-in UID
            //if basedOnUID {
             //   query = query.whereField("userUID", isEqualTo: uid)
           // }
            
            let docs = try await query.getDocuments()
            let fetchedPosts = try docs.documents.compactMap { document -> Post in
                try document.data(as: Post.self)
            }
            
            //saving last fetched doc so that it can be used for pagination in Firestore
            await MainActor.run(body: {
                posts.append(contentsOf: fetchedPosts)
                paginationDoc = docs.documents.last
                isFetching = false
            })
        } catch {
            self.authService.errorMessage = error.localizedDescription
           // await setErrorWithMessage("Error", error.localizedDescription, handler: {})
        }
    }
}

struct ChurchPostsView: View {
    @State private var recentPosts: [Post] = []
    @State private var createNewPost: Bool = false
    @State private var flipAngle = Double.zero
    let txt = Array("New Life Global Feed")
    
    private func animateTwice() {
        withAnimation {
            flipAngle = 360
        }
        
        // Reverse the animation after 1.5 seconds (or adjust the delay based on timing needs)
        DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) {
            withAnimation {
                flipAngle = .zero
            }
        }
        
        // Run the second animation cycle after 3 seconds
        DispatchQueue.main.asyncAfter(deadline: .now() + 3) {
            withAnimation {
                flipAngle = 360
            }
        }
    }
    
    var body: some View {
        ZStack {
            VStack {
                HStack {
                    HStack(spacing: 0) {
                        ForEach(0..<txt.count, id: \.self) { flip in
                            GradientText(text: String(txt[flip]), boldFontModifiersEnabled: true)
                                .rotation3DEffect(.degrees(flipAngle), axis: (x: 1, y: 1, z: 1))
                                .animation(.default.delay(Double(flip) * 0.1), value: flipAngle)
                                .foregroundColor(.white)
                        }
                    }
                    .padding(.horizontal)
                        
                    Spacer()
                    GradientIcon(iconName: "plus")
                        .frame(width: 30, height: 30)
                        .onTapGesture {
                            if let current = Auth.auth().currentUser, current.isAnonymous {
                                showAutoDismissingAlert("⚠️", "Unable to create content for anonymous user. Please sign up, or login to continue")
                            } else {
                                createNewPost.toggle()
                            }
                        }
                        .background(
                            Capsule()
                                .fill(LinearGradient(colors: [Color("majenta"), Color("purple")], startPoint: .topLeading, endPoint: .bottomTrailing))
                                .frame(width: 30)
                        )
                        .padding(.horizontal, .small)
                }
                .padding(.medium)
                Divider()

                Spacer()
                
                ReusableChurchPostsView(posts: $recentPosts)
                    .horizontalAlign(.center)
                    .verticalAlign(.center)
                    .overlay(alignment: .bottomTrailing) {
                        
                    }
            }
            .fullScreenCover(isPresented: $createNewPost) {
                createNewPost = false
            } content: {
                ChurchNewPost { post in
                    //insert created post at the top on the recent posts list
                    recentPosts.insert(post, at: 0)
                }
            }
        }
        .background(
            ZStack {
                Image("background1")
                    .resizable()
                    .aspectRatio(contentMode: .fill)
                    .edgesIgnoringSafeArea(.all)
                Color("secondaryBackground")
                    .edgesIgnoringSafeArea(.all)
                    .opacity(0.7)
                
                VisualEffectBlur(blurStyle: .systemUltraThinMaterial, vibrancyStyle: .fill) {
                    
                }
            }
        )
        .onAppear(perform: {
            animateTwice()
        })
    }
}

struct CellGroup: Identifiable, Codable {
    @DocumentID var id: String?
    var name: String
    var locationOnMap: GeoPoint? // Use GeoPoint for location
    var members: [String] // Array of AppUser IDs (or AppUser.email if uids are not available).
    var leaderName: String
    var category: CellGroupCategory
    var isApproved: Bool = false//Add to the Cell Group List
    var approvedBy: String? // Name of the approver (or nil if not approved)
}

enum CellGroupCategory: String, Codable, CaseIterable {
    case women = "Women's Cell"
    case men = "Men's Cell"
    case youth = "Youth Cell"
    case family = "Family Cell"
    case other = "Other"
    
    var displayName: String {
        return self.rawValue
    }
}

class CellGroupViewModel: ObservableObject {
    @Published var cellGroups: [CellGroup] = []
    @Published var errorMessage: String? = nil
    @Published var showAllGroups = false //Add to check if the cell group is made by a ministry leader/pastor

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?

    init() {
        loadData()
    }

    func loadData() {
        var query: Query = db.collection("cellGroups")
            .order(by: "name")

        if !showAllGroups {
            query = query.whereField("isApproved", isEqualTo: true)
        }

        listenerRegistration = query.addSnapshotListener { (querySnapshot, error) in
            if let error = error {
                self.errorMessage = "Error getting cell groups: \(error.localizedDescription)"
                print("Error getting cell groups: \(error)")
                return
            }

            self.cellGroups = querySnapshot?.documents.compactMap { document in
                try? document.data(as: CellGroup.self)
            } ?? []
        }
    }

    deinit {
        listenerRegistration?.remove()
    }

    func addCellGroup(name: String, locationOnMap: GeoPoint?, members: [String], leaderName: String, category: CellGroupCategory) {
        let newCellGroup = CellGroup(name: name, locationOnMap: locationOnMap, members: members, leaderName: leaderName, category: category)
        do {
            _ = try db.collection("cellGroups").addDocument(from: newCellGroup)
        } catch {
            errorMessage = "Error adding cell group: \(error.localizedDescription)"
            print("Error adding cell group: \(error)")
        }
    }

    func updateCellGroup(cellGroup: CellGroup) {
        guard let cellGroupId = cellGroup.id else { return }
        do {
            try db.collection("cellGroups").document(cellGroupId).setData(from: cellGroup)
        } catch {
            errorMessage = "Error updating cell group: \(error.localizedDescription)"
            print("Error updating cell group: \(error)")
        }
    }

    func deleteCellGroup(cellGroup: CellGroup) {
        guard let cellGroupId = cellGroup.id else { return }
        db.collection("cellGroups").document(cellGroupId).delete() { error in
            if let error = error {
                self.errorMessage = "Error deleting cell group: \(error.localizedDescription)"
                print("Error deleting cell group: \(error)")
            }
        }
    }

    func approveCellGroup(cellGroup: CellGroup, approvedBy: String) {
        var updatedCellGroup = cellGroup
        updatedCellGroup.isApproved = true
        updatedCellGroup.approvedBy = approvedBy

        updateCellGroup(cellGroup: updatedCellGroup)
    }
}

struct CellGroupLocationPickerView: View {
    @Binding var cellGroup: CellGroup //Pass the Binding for CellGroup
    @Environment(\.dismiss) var dismiss

    @State private var region: MKCoordinateRegion
    @State private var selectedCoordinate: CLLocationCoordinate2D?

    init(cellGroup: Binding<CellGroup>) {
        self._cellGroup = cellGroup
        let initialCoordinate = CLLocationCoordinate2D(
            latitude: 37.7749,
            longitude: -122.4194
        )
        self._region = State(initialValue: MKCoordinateRegion(
            center: initialCoordinate,
            span: MKCoordinateSpan(latitudeDelta: 0.05, longitudeDelta: 0.05)
        ))
    }

    var body: some View {
        NavigationView {
            ZStack {
                ChurchMapViewWrapper(region: $region, selectedCoordinate: $selectedCoordinate)
                    .edgesIgnoringSafeArea(.all)

                VStack {
                    Spacer()
                    if let coordinate = selectedCoordinate {
                        Text("Selected: \(coordinate.latitude.formatted()), \(coordinate.longitude.formatted())")
                            .padding()
                            .background(.ultraThinMaterial)
                            .cornerRadius(8)
                            .padding()
                    }
                }
            }
            .navigationTitle("Select Cell Group Location")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }

                ToolbarItem(placement: .confirmationAction) {
                    Button("Save") {
                        if let selectedCoordinate {
                            cellGroup.locationOnMap = GeoPoint(
                                latitude: selectedCoordinate.latitude,
                                longitude: selectedCoordinate.longitude
                            )
                        }
                        dismiss()
                    }
                    .disabled(selectedCoordinate == nil)
                }
            }
        }
        .onAppear {
            if let existingLocation = cellGroup.locationOnMap {
                region = MKCoordinateRegion(
                    center: CLLocationCoordinate2D(
                        latitude: existingLocation.latitude,
                        longitude: existingLocation.longitude
                    ),
                    span: MKCoordinateSpan(latitudeDelta: 0.05, longitudeDelta: 0.05)
                )
                selectedCoordinate = CLLocationCoordinate2D(
                    latitude: existingLocation.latitude,
                    longitude: existingLocation.longitude
                )
            }
        }
    }
}

struct ChurchMapViewWrapper: UIViewRepresentable {
    @Binding var region: MKCoordinateRegion
    @Binding var selectedCoordinate: CLLocationCoordinate2D?
    
    func makeUIView(context: Context) -> MKMapView {
        let mapView = MKMapView()
        mapView.delegate = context.coordinator
        mapView.region = region
        mapView.addGestureRecognizer(
            UITapGestureRecognizer(
                target: context.coordinator,
                action: #selector(Coordinator.handleTap)
            )
        )
        return mapView
    }
    
    func updateUIView(_ mapView: MKMapView, context: Context) {
        // Update annotation
        mapView.removeAnnotations(mapView.annotations)
        if let coordinate = selectedCoordinate {
            let annotation = MKPointAnnotation()
            annotation.coordinate = coordinate
            mapView.addAnnotation(annotation)
        }
    }
    
    func makeCoordinator() -> Coordinator {
        Coordinator(self)
    }
    
    class Coordinator: NSObject, MKMapViewDelegate {
        var parent: ChurchMapViewWrapper
        
        init(_ parent: ChurchMapViewWrapper) {
            self.parent = parent
        }
        
        @objc func handleTap(gesture: UITapGestureRecognizer) {
            let mapView = gesture.view as! MKMapView
            let point = gesture.location(in: mapView)
            let coordinate = mapView.convert(point, toCoordinateFrom: mapView)
            
            parent.selectedCoordinate = coordinate
            
            // Update region to center on new coordinate
            parent.region = MKCoordinateRegion(
                center: coordinate,
                span: parent.region.span
            )
        }
        
        func mapView(_ mapView: MKMapView, viewFor annotation: MKAnnotation) -> MKAnnotationView? {
            let identifier = "Placemark"
            
            var annotationView = mapView.dequeueReusableAnnotationView(withIdentifier: identifier)
            if annotationView == nil {
                annotationView = MKMarkerAnnotationView(annotation: annotation, reuseIdentifier: identifier)
                annotationView?.canShowCallout = true
            } else {
                annotationView?.annotation = annotation
            }
            
            return annotationView
        }
    }
}

struct AddCellGroupView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var cellGroupViewModel: CellGroupViewModel
    @State private var name = ""
    @State private var leaderName = ""
    @State private var members: [String] = [] //TODO: change this
    @State private var category: CellGroupCategory = .family
    @State private var showingLocationPicker = false

    @State private var cellGroup = CellGroup(name: "", locationOnMap: nil, members: [], leaderName: "", category: .family, isApproved: false)

    var body: some View {
        NavigationView {
            Form {
                TextField("Cell Group Name", text: $name)
                TextField("Leader Name", text: $leaderName) // Assuming you'll implement a proper user selection mechanism later

                //TODO Implement the ability to add other people to the code as well
                Picker("Category", selection: $category) {
                    ForEach(CellGroupCategory.allCases, id: \.self) { category in
                        Text(category.displayName).tag(category)
                    }
                }
                .pickerStyle(.menu)

                Section {
                    Button("Select Location") {
                        showingLocationPicker = true
                    }
                }

                Button("Add Cell Group") {
                    cellGroupViewModel.addCellGroup(name: name, locationOnMap: cellGroup.locationOnMap, members: members, leaderName: leaderName, category: category)
                    dismiss()
                }
            }
            .navigationTitle("Add Cell Group")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
            .sheet(isPresented: $showingLocationPicker) {
               CellGroupLocationPickerView(cellGroup: $cellGroup)
            }
        }
    }
}

struct Announcement: Identifiable, Codable {
    @DocumentID var id: String?
    var title: String
    var description: String
    var style: AnnouncementStyle
    var date: Date // Changed to Date object for the Picker
    var imageUrl: String? // Added for image upload
    var offset: CGFloat = 0
    
    // Helper to format date for display (matches your UI requirements)
    var formattedDate: String {
        let formatter = DateFormatter()
        formatter.dateStyle = .medium
        formatter.timeStyle = .short
        return formatter.string(from: date)
    }
    
    // Enum coding keys to exclude 'offset' from Firebase
    enum CodingKeys: String, CodingKey {
        case id, title, description, style, date, imageUrl
    }
}

enum AnnouncementStyle: String, Codable, CaseIterable {
    case informational = "Informational"
    case warning = "Warning"
    case update = "Update"
    case promotional = "Promotional"
    
    var displayName: String { rawValue }
    
    var gradient: LinearGradient {
        switch self {
        case .informational:
            return LinearGradient(colors: [.cyan, .blue], startPoint: .top, endPoint: .bottom)
        case .warning:
            return LinearGradient(colors: [.orange, .red], startPoint: .top, endPoint: .bottom)
        case .update:
            return LinearGradient(colors: [.mint, .green], startPoint: .top, endPoint: .bottom)
        case .promotional:
            return LinearGradient(colors: [.purple, .pink], startPoint: .top, endPoint: .bottom)
        }
    }
}

class AnnouncementCarouselViewModel: ObservableObject {
    @Published var announcements: [Announcement] = []
    @Published var swipedAnnouncement = 0
    @Published var showAnnouncement = false
    @Published var selectedAnnouncement: Announcement? = nil
    @Published var showContent = false
    @Published var isLoading = false
    @Published var errorMessage: String? = nil

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?
    
    init() {
        loadData()
    }

    func loadData() {
        listenerRegistration = db.collection("announcements")
            .order(by: "date", descending: false) // Show soonest events first? Or newest created?
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting announcements: \(error.localizedDescription)"
                    return
                }
                
                self.announcements = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: Announcement.self)
                } ?? []
            }
    }
    
    // Function to upload image and save data
    func addAnnouncement(title: String, description: String, date: Date, style: AnnouncementStyle, imageData: Data?) {
        self.isLoading = true
        
        let saveDocument = { (url: String?) in
            let newAnnouncement = Announcement(
                title: title,
                description: description,
                style: style,
                date: date,
                imageUrl: url
            )
            
            do {
                _ = try self.db.collection("announcements").addDocument(from: newAnnouncement)
                self.isLoading = false
            } catch {
                self.errorMessage = "Error saving to Firestore: \(error.localizedDescription)"
                self.isLoading = false
            }
        }
        
        if let imageData = imageData {
            // Upload Image first
            let storageRef = Storage.storage().reference().child("announcement_images/\(UUID().uuidString).jpg")
            
            storageRef.putData(imageData, metadata: nil) { _, error in
                if let error = error {
                    self.errorMessage = "Image upload error: \(error.localizedDescription)"
                    self.isLoading = false
                    return
                }
                
                storageRef.downloadURL { url, error in
                    if let error = error {
                        self.errorMessage = "Error getting URL: \(error.localizedDescription)"
                        self.isLoading = false
                        return
                    }
                    saveDocument(url?.absoluteString)
                }
            }
        } else {
            // Save without image
            saveDocument(nil)
        }
    }
    
    func deleteAnnouncement(announcement: Announcement) {
        guard let id = announcement.id else { return }
        
        // Optional: Delete image from storage if exists (omitted for brevity)
        
        db.collection("announcements").document(id).delete { error in
            if let error = error {
                self.errorMessage = "Error deleting: \(error.localizedDescription)"
            }
        }
    }

    deinit {
        listenerRegistration?.remove()
    }
}

struct AnnouncementCardView: View {
    @EnvironmentObject var announcementCarouselVM: AnnouncementCarouselViewModel
    var announcement: Announcement
    var animation: Namespace.ID

    var body: some View {
        VStack {
            Text(announcement.formattedDate)
                .font(.caption)
                .foregroundColor(Color.white.opacity(0.85))
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding()
                .padding(.top, 10)
                .matchedGeometryEffect(id: "Date-\(announcement.id)", in: animation)

            HStack {
                Text(announcement.title)
                    .font(.title)
                    .fontWeight(.bold)
                    .foregroundColor(.white)
                    .frame(width: 250, alignment: .leading)
                    .padding()
                    .matchedGeometryEffect(id: "Title-\(announcement.id)", in: animation)

                Spacer(minLength: 0)
            }

            Spacer(minLength: 0)

            if #available(iOS 26.0, *) {
                HStack {
                    Spacer(minLength: 0)
                    
                    if !announcementCarouselVM.showContent {
                        HStack {
                            Text("Read more")
                            
                            Image(systemName: "arrow.right")
                        }
                        .padding(.horizontal)
                        .padding(.vertical, 8)
                        .glassEffect(in: Capsule())
                    }
                }
                .foregroundColor(Color.white.opacity(0.9))
                .padding(30)
            } else {
                    // Fallback on earlier versions
                HStack {
                    Spacer(minLength: 0)
                    
                    if !announcementCarouselVM.showContent {
                        Text("Read more")
                        
                        Image(systemName: "arrow.right")
                    }
                }
                .foregroundColor(Color.white.opacity(0.9))
                .padding(30)
            }
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(
            announcement.style.gradient
                .cornerRadius(25)
                .matchedGeometryEffect(id: "bgColor-\(announcement.id)", in: animation)
        )
        .onTapGesture {
            withAnimation(.spring()) {
                announcementCarouselVM.selectedAnnouncement = announcement
                announcementCarouselVM.showAnnouncement.toggle()
                DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                    withAnimation(.easeIn) {
                        announcementCarouselVM.showContent.toggle()
                    }
                }
            }
        }
    }
}

struct AddAnnouncementView: View {
    @Environment(\.dismiss) var dismiss
    @EnvironmentObject var announcementVM: AnnouncementCarouselViewModel
    
    @State private var title = ""
    @State private var description = ""
    @State private var date = Date()
    @State private var style: AnnouncementStyle = .informational
    
    // Image Picking
    @State private var showPhotoPicker = false
    @State private var avatarImage: UIImage?
    @State private var imageData: Data?
    
    var body: some View {
        NavigationView {
            ZStack {
                Form {
                    Section(header: Text("Details")) {
                        TextField("Title", text: $title)
                        
                        Picker("Style", selection: $style) {
                            ForEach(AnnouncementStyle.allCases, id: \.self) { style in
                                Text(style.displayName).tag(style)
                            }
                        }
                    }
                    
                    Section(header: Text("When")) {
                        DatePicker("Date & Time", selection: $date, displayedComponents: [.date, .hourAndMinute])
                    }
                    
                    Section(header: Text("Content")) {
                        TextEditor(text: $description)
                            .frame(height: 100)
                            .overlay(
                                Group {
                                    if description.isEmpty {
                                        Text("Description...")
                                            .foregroundColor(.gray)
                                            .padding(.horizontal, 4)
                                            .padding(.vertical, 8)
                                            .allowsHitTesting(false)
                                            .frame(maxWidth: .infinity, alignment: .topLeading)
                                    }
                                }
                            )
                    }
                    
                    Section(header: Text("Image (Optional)")) {
                        if let avatarImage = avatarImage {
                            Image(uiImage: avatarImage)
                                .resizable()
                                .scaledToFit()
                                .frame(height: 200)
                                .cornerRadius(10)
                                .onTapGesture {
                                    showPhotoPicker = true
                                }
                        } else {
                            Button {
                                showPhotoPicker = true
                            } label: {
                                Label("Select Image", systemImage: "photo")
                            }
                        }
                    }
                    
                    Button {
                        announcementVM.addAnnouncement(
                            title: title,
                            description: description,
                            date: date,
                            style: style,
                            imageData: imageData
                        )
                        dismiss()
                    } label: {
                        if announcementVM.isLoading {
                            ProgressView()
                        } else {
                            Text("Post Announcement")
                                .frame(maxWidth: .infinity)
                                .foregroundColor(.blue)
                        }
                    }
                    .disabled(title.isEmpty || description.isEmpty || announcementVM.isLoading)
                }
                
                if let error = announcementVM.errorMessage {
                    VStack {
                        Spacer()
                        Text(error)
                            .foregroundColor(.white)
                            .padding()
                            .background(Color.red.cornerRadius(10))
                            .padding()
                    }
                }
            }
            .navigationTitle("New Announcement")
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") { dismiss() }
                }
            }
            .sheet(isPresented: $showPhotoPicker) {
                PhotoPicker(avatar: $avatarImage)
            }
            .onChange(of: avatarImage) { newImage in
                if let newImage = newImage {
                    self.imageData = newImage.jpegData(compressionQuality: 0.5)
                }
            }
        }
    }
}

struct ToggleButton: View {
    var icon: String?
    var foregroundColor: Color = .white
    var action: () -> ()
    var body: some View {
        Button {
            action()
        } label: {
            Image(systemName: icon ?? "switch.2")
                .font(.system(size: 15, weight: .regular))
                .padding(.all, 8)
                .foregroundColor(foregroundColor)
                .background(Color.black.opacity(0.6))
                .mask(Circle())
        }
    }
}

struct AnnouncementDetailView: View {
    @EnvironmentObject var announcementCarouselVM: AnnouncementCarouselViewModel
    var animation: Namespace.ID

    var body: some View {
        ZStack {
            VStack {
                HStack {
                    ToggleButton(icon: "chevron.left") {
                        withAnimation(.spring()) {
                            announcementCarouselVM.selectedAnnouncement = nil
                            announcementCarouselVM.showAnnouncement.toggle()
                            DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                                withAnimation(.easeIn) {
                                    announcementCarouselVM.showContent.toggle()
                                }
                            }
                        }
                    }
                    .padding(.horizontal, 4)
                    
                    Text(announcementCarouselVM.selectedAnnouncement?.formattedDate ?? "Today")
                        .font(.caption)
                        .foregroundColor(Color.white.opacity(0.85))
                        .frame(maxWidth: .infinity, alignment: .leading)
                        .padding()
                        .padding(.top, 10)
                        .matchedGeometryEffect(id: "Date-\(announcementCarouselVM.selectedAnnouncement?.id ?? "")", in: animation)
                }

                HStack {
                    Text(announcementCarouselVM.selectedAnnouncement?.title ?? "No title")
                        .font(.title)
                        .fontWeight(.bold)
                        .foregroundColor(.white)
                        .frame(width: 250, alignment: .leading)
                        .padding()
                        .matchedGeometryEffect(id: "Title-\(announcementCarouselVM.selectedAnnouncement?.id ?? "")", in: animation)

                    Spacer(minLength: 0)
                }

                // Detail text content
                // Show content some delay for better animation
                if announcementCarouselVM.showContent && announcementCarouselVM.selectedAnnouncement?.description != nil {
                    Text(announcementCarouselVM.selectedAnnouncement!.description)
                        .fontWeight(.semibold)
                        .foregroundColor(.white)
                        .padding()
                }

                Spacer(minLength: 0)
            }
            .frame(maxWidth: .infinity, maxHeight: .infinity)
            .background(
                announcementCarouselVM.selectedAnnouncement?.style.gradient ?? Color.gray
                    .cornerRadius(25)
                    .matchedGeometryEffect(id: "bgColor-\(announcementCarouselVM.selectedAnnouncement?.id ?? "")", in: animation)
                    .ignoresSafeArea(.all, edges: .bottom) as? LinearGradient
            )
        }
    }
}

struct UpcomingEvent: Identifiable {
    var id = UUID().uuidString
    var title: String
    var description: String
    var style: EventStyle
    var date: String
    var offset: CGFloat = 0
}

enum EventStyle {
    case conference
    case holiday
    case gala
    
    var gradient: LinearGradient {
        switch self {
        case .conference:
            // Was .pink
            return LinearGradient(colors: [.pink, .purple], startPoint: .topLeading, endPoint: .bottomTrailing)
            
        case .holiday:
            // Was .red
            return LinearGradient(colors: [.red, .orange], startPoint: .topLeading, endPoint: .bottomTrailing)
            
        case .gala:
            // Was .purple
            return LinearGradient(colors: [.purple, .indigo], startPoint: .topLeading, endPoint: .bottomTrailing)
        }
    }
}

class UpcomingEventsCarouselViewModel: ObservableObject {
    @Published var events: [UpcomingEvent] = [
        UpcomingEvent(
            title: "Youth Conference: Campus Rush",
            description: """
            Prepare for a supernatural encounter that will set your heart ablaze for God! The Sons & Daughters of Apostle Miz Mzwakhe Tancredi are gathering for our annual Campus Rush Youth Conference, a pivotal weekend dedicated to fellowship, impartation, and divine activation. This is far more than an event; it is a summons to a generation destined for greatness. Under the powerful apostolic grace of our father, Apostle Miz Mzwakhe Tancredi, you will be immersed in dynamic worship that touches the heavens and deep teachings that will equip you to fulfill your God-given mandate. We are believing for a mighty outpouring of the Holy Spirit, where destinies are clarified, gifts are stirred up, and a holy fire is ignited within every attendee.

            This conference is specifically designed for young people who refuse to settle for the ordinary. It is for those who are hungry for a tangible move of God and are ready to be launched into a new dimension of their walk with Christ. Come expecting to receive prophetic direction for your life, your studies, and your future. Join hundreds of other passionate young believers as we contend for a revival that will sweep through our university campuses and our nation. Your life is a prophecy waiting to be fulfilled. Don't miss this divine appointment to be commissioned into your purpose. Register today and secure your place in the rush!
            """,
            style: .conference,
            date: "November 10-12"
        ),
        UpcomingEvent(
            title: "Christmas Celebration Service",
            description: """
            Join the Miz Mzwakhe Tancredi Ministries family for a truly glorious Christmas Celebration Service, where we honor the birth of our King, Jesus Christ. Step into an atmosphere saturated with the tangible presence of God as our renowned worship team leads us in prophetic worship and triumphant Christmas carols that declare the majesty of the season. This special service is a cornerstone of our year, a time where we gather as one body to reflect on the profound love and hope that came to the world through the manger. It is an opportunity for families to unite and for hearts to be filled with the warmth and joy of God’s amazing grace.

            The highlight of our celebration will be a life-changing word delivered by our very own Apostle Miz Mzwakhe Tancredi. Prepare your spirit to receive a fresh revelation of what Christ's birth means for us today and a potent prophetic word to carry you through the festive season and beyond. We are not just remembering a historical event; we are celebrating the living King who reigns forevermore! Bring your loved ones, invite your neighbors, and come experience a Christmas celebration filled with wonder, power, and the glorious good news of our Savior. Let’s make this Christmas the most memorable one yet, together in His presence.
            """,
            style: .holiday,
            date: "December 24"
        ),
        UpcomingEvent(
            title: "New Year's Eve Prophetic Gala",
            description: """
            Do not enter the new year by chance; cross over by divine strategy and apostolic decree! You are cordially invited to our New Year's Eve Prophetic Gala, the most significant night of our calendar. This is a sacred and celebratory assembly where we will shut the gates of the past and prophetically open the doors to the future God has ordained for us. Join Apostle Miz Mzwakhe Tancredi and the entire MTM family for an unforgettable evening of high praise, fervent prayer, and powerful prophetic ministry. This is your moment to receive clear apostolic direction for the year ahead, ensuring you walk in victory, purpose, and unprecedented favor.

            The evening will be filled with an electric atmosphere of faith as we decree and declare the promises of God over our lives, families, careers, and our nation. As the clock strikes midnight, we will not just be celebrating a new date; we will be stepping into a new season, armed with a fresh word from the Lord. This is your opportunity to be positioned for blessing and to receive impartation for supernatural breakthroughs in the coming year. Dress in your finest attire and come prepared to worship, dance, and prophesy your way into a year of more. Secure your place for this landmark event and prepare to be launched into 2025 with power and authority.
            """,
            style: .gala,
            date: "December 31"
        )
    ]

    @Published var swipedEvent = 0
    @Published var showEvent = false
    @Published var selectedEvent: UpcomingEvent? = nil
    @Published var showContent = false // This will handle Detail content
}

struct UpcomingEventCardView: View {
    @EnvironmentObject var model: UpcomingEventsCarouselViewModel
    var event: UpcomingEvent
    var animation: Namespace.ID

    var body: some View {
        VStack {
            Text(event.date)
                .font(.caption)
                .foregroundColor(Color.white.opacity(0.85))
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding()
                .padding(.top, 10)
                .matchedGeometryEffect(id: "Date-\(event.id)", in: animation)

            HStack {
                Text(event.title)
                    .font(.title)
                    .fontWeight(.bold)
                    .foregroundColor(.white)
                    .frame(width: 250, alignment: .leading)
                    .padding()
                    .matchedGeometryEffect(id: "Title-\(event.id)", in: animation)

                Spacer(minLength: 0)
            }

            Spacer(minLength: 0)

            HStack {
                Spacer(minLength: 0)

                if !model.showContent {
                    if #available(iOS 26.0, *) {
                        HStack {
                            Text("Read more")
                            
                            Image(systemName: "arrow.right")
                        }
                        .padding(.horizontal)
                        .padding(.vertical, 8)
                        .glassEffect(in: Capsule())
                    } else {
                            // Fallback on earlier versions
                        HStack {
                            Text("Read more")
                            
                            Image(systemName: "arrow.right")
                        }
                        .padding(.horizontal)
                        .padding(.vertical, 8)
                    }
                }
            }
            .foregroundColor(Color.white.opacity(0.9))
            .padding(30)
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(
            event.style.gradient
                .cornerRadius(25)
                .matchedGeometryEffect(id: "bgColor-\(event.id)", in: animation)
        )
        .onTapGesture {
            withAnimation(.spring()) {
               // if let an = announcement as? Announcement{

               // }
                model.selectedEvent = event
                model.showEvent.toggle()
                DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                    withAnimation(.easeIn) {
                        model.showContent.toggle()
                    }
                }
            }
        }
    }
}

struct StackingContentView: View {
    var body: some View {
        StackingContentHome()
    }
}

struct StackingContentView_Previews: PreviewProvider {
    static var previews: some View {
        StackingContentView()
    }
}

struct StackedCardView: View {
    var tool: Tool
    var reader: GeometryProxy
    @Binding var swiped: Int
    @Binding var show: Bool
    @Binding var selected: Tool
    var name: Namespace.ID

    var body: some View {
        VStack {
            Spacer(minLength: 0)

            ZStack(alignment: Alignment(horizontal: .trailing, vertical: .bottom), content: {
                VStack {
                    Image(tool.image)
                        .resizable()
                        .aspectRatio(contentMode: .fit)
                        .matchedGeometryEffect(id: tool.image, in: name)
                        .padding(.top)
                        .padding(.horizontal, 25)

                    HStack {
                        VStack(alignment: .leading, spacing: 12, content: {
                            Text(tool.name)
                                .font(.system(size: 40))
                                .fontWeight(.bold)
                                .foregroundColor(.black)

                            Text("Design tool")
                                .font(.system(size: 20))
                                .foregroundColor(.black)

                            Button(action: {
                                withAnimation(.spring()) {
                                    selected = tool
                                    show.toggle()
                                }
                            }, label: {
                                Text("Know More >")
                                    .font(.system(size: 20))
                                    .fontWeight(.bold)
                                    .foregroundColor(.pink)
                            })
                            .padding(.top)
                        })

                        Spacer(minLength: 0)
                    }
                    .padding(.horizontal, 30)
                    .padding(.bottom, 15)
                    .padding(.top, 25)
                }

                HStack {
                    Text("#")
                        .font(.system(size: 60))
                        .fontWeight(.bold)
                        .foregroundColor(Color.gray.opacity(0.6))

                    Text("\(tool.place)")
                        .font(.system(size: 120))
                        .fontWeight(.bold)
                        .foregroundColor(Color.gray.opacity(0.6))
                }
                .offset(x: -15, y: 25)
            })
            .frame(height: reader.frame(in: .global).height - 120)
            .padding(.vertical, 10)
            .background(Color.white)
            .cornerRadius(25)
            .padding(.horizontal, 30 + (CGFloat(tool.id - swiped) * 10))
            .offset(y:tool.id - swiped <= 2 ? CGFloat(tool.id - swiped) * 25 : 50)
            .shadow(color: Color.black.opacity(0.12), radius: 5, x: 0, y: 5)

            Spacer(minLength: 0)
        }
        .contentShape(Rectangle())
    }
}

struct Detail: View {
    var tool: Tool
    @Binding var show: Bool
    var name: Namespace.ID

    var body: some View {
        VStack {
            HStack(alignment: .top, spacing: 12, content: {
                Button(action: {
                    withAnimation(.spring()) {
                        show.toggle()
                    }
                }, label: {
                    Image(systemName: "chevron.left")
                        .font(.system(size: 30, weight: .bold))
                        .foregroundColor(.black)
                })

                Spacer(minLength: 0)

                Image(tool.image)
                    .resizable()
                    .aspectRatio(contentMode: .fit)
                    .frame(width: 200, height: 200)
                    .matchedGeometryEffect(id: tool.image, in: name)
            })
            .padding(.leading, 20)
            .padding([.top, .bottom, .trailing])

            // For smaller size phones
            ScrollView(.vertical, showsIndicators: false, content: {
                VStack {
                    HStack {
                        VStack(alignment: .leading, spacing: 12, content: {
                            Text(tool.name)
                                .font(.system(size: 45, weight: .bold))
                                .foregroundColor(.black)

                            Text("Design tools")
                                .font(.system(size: 30))
                                .foregroundColor(.black)

                            Text("Free")
                                .font(.title)
                                .fontWeight(.bold)
                                .foregroundColor(.blue)
                                .padding(.top, 10)
                        })

                        Spacer(minLength: 0)

                        HStack {
                            Text("#")
                                .font(.system(size: 60, weight: .bold))

                            Text("\(tool.place)")
                                .font(.system(size: 180, weight: .bold))
                        }
                        .foregroundColor(Color.gray.opacity(0.7))
                    }
                    .padding(.vertical)


                    Text("\(tool.name) is s vector graphics editor and prototyping tool. It is primarily web-based, with additional offline features enabled by desktop applications for macOS and Windows.")
                        .font(.system(size: 22))
                        .foregroundColor(Color.black.opacity(0.7))
                        .multilineTextAlignment(.leading)
                        .padding(.top)

                    HStack(spacing: 15) {
                        Button(action: {}, label: {
                            Text("Website")
                                .fontWeight(.bold)
                                .foregroundColor(.white)
                                .padding(.vertical)
                                .frame(width: UIScreen.main.bounds.width - 120)
                                .background(Color("orange"))
                                .clipShape(Capsule())
                        })

                        Button(action: {}, label: {
                            Image(systemName: "suit.heart.fill")
                                .font(.title)
                                .foregroundColor(Color("orange"))
                                .padding()
                                .background(Color.white)
                                .clipShape(Circle())
                                .shadow(radius: 3)
                        })
                    }
                    .padding(.top, 25)
                    .padding(.bottom)
                }
                .padding(.horizontal, 20)
            })
        }
        .background(Color.white)
    }
}

struct StackingContentHome: View {
    @State var designTools = [
        Tool(id: 0, image: "healingacademy", name: "Healing Academy", offset: 0, place: 1),
        Tool(id: 1, image: "campusrush", name: "Campus Rush", offset: 0, place: 2),
        Tool(id: 2, image: "adoptalanguage", name: "Adopt a Language", offset: 0, place: 3),
        Tool(id: 3, image: "tancredifoundation", name: "Tancredi Foundation", offset: 0, place: 4),
        Tool(id: 4, image: "new-life-childrens-ministry", name: "New Life Children's Ministry", offset: 0, place: 5),
//        Tool(id: 5, image: "invision", name: "Invision", offset: 0, place: 6),
//        Tool(id: 6, image: "affinity", name: "Affinity Photos", offset: 0, place: 7)
    ]
    // To track which card is swiped
    @State var swiped = 0
    @Namespace var name
    @State var selected = Tool(id: 0, image: "sketch", name: "Sketch", offset: 0, place: 1)
    @State var show = false

    var body: some View {
        ZStack {
            VStack {
                HStack {
                    VStack(alignment: .leading, spacing: 12, content: {
                        Text("Products")
                            .font(.system(size: 45))
                            .foregroundColor(.white)

                        HStack(spacing: 15) {
                            Text("Design tools")
                                .font(.system(size: 30))
                                .fontWeight(.bold)
                                .foregroundColor(Color.white.opacity(0.7))

                            Button(action: {}, label: {
                                Image(systemName: "chevron.down")
                                    .font(.system(size: 30))
                                    .foregroundColor(Color("orange"))
                            })
                        }
                    })

                    Spacer(minLength: 0)
                }
                .padding()

                // Stacked Elements
                GeometryReader { reader in
                    ZStack {
                        // ZStack will overlay on one another so revesing
                        ForEach(designTools.reversed()) { tool in
                            StackedCardView(tool: tool, reader: reader, swiped: $swiped, show: $show, selected: $selected, name: name)
                                .offset(x: tool.offset)
                                .rotationEffect(.init(degrees: getRotation(offset: tool.offset)))
                                .gesture(
                                    DragGesture()
                                        .onChanged({ value in
                                            // Update position
                                            withAnimation {
                                                // Only left swipe
                                                if value.translation.width > 0 {
                                                    designTools[tool.id].offset = value.translation.width
                                                }
                                            }
                                        })
                                        .onEnded({ value in
                                            withAnimation {
                                                if value.translation.width > 150 {
                                                    designTools[tool.id].offset = 1000
                                                    // Update swipe id
                                                    // Since its starting from 0
                                                    swiped = tool.id + 1

                                                    restoreCard(id: tool.id)
                                                } else {
                                                    designTools[tool.id].offset = 0
                                                }
                                            }
                                        })
                                )
                        }
                    }
                    .offset(y: -25)
                }
            }

            if show {
                Detail(tool: selected, show: $show, name: name)
            }
        }
        .background(
            LinearGradient(gradient: .init(colors: [Color("top"), Color("center"), Color("bottom")]), startPoint: .top, endPoint: .bottom)
                .edgesIgnoringSafeArea(.all)
                // Disable bg color when its expanded
                .opacity(show ? 0 : 1)
        )
    }

    // Add card to list
    func restoreCard(id: Int) {
        var currentCard = designTools[id]
        // append last
        currentCard.id = designTools.count

        designTools.append(currentCard)

        // Go back effect
        DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
            withAnimation {
                // last one we append
                designTools[designTools.count - 1].offset = 0
            }
        }
    }

    // Rotation
    func getRotation(offset: CGFloat) -> Double {
        let value = offset / 150

        // You can give your own angle here
        let angle: CGFloat = 5

        let degree = Double(value * angle)

        return degree
    }
}

struct Tool: Identifiable {
    var id: Int
    var image: String
    var name: String
    var offset: CGFloat
    var place: Int
}

struct SermonContentView: View {
    var body: some View {
        SermonHome()
    }
}

//struct SermonContentView_Previews: PreviewProvider {
//    static var previews: some View {
//        SermonContentView()
//    }
//}

struct SermonView: View {
    var album: Album
    var body: some View {
        HStack {
            Image(album.albumCover)
                .resizable()
                .frame(width: 100, height: 100)
                .cornerRadius(15)

            VStack(alignment: .leading, spacing: 12, content: {
                Text(album.albumName)
                    .fontWeight(.bold)

                Text(album.albumAuthor)
            })
            .padding(.leading, 10)

            Spacer(minLength: 0)
        }
        .background(Color.white.shadow(color: Color.black.opacity(0.12), radius: 5, x: 0, y: 4))
        .cornerRadius(15)
    }
}

struct SermonHome: View {
    var body: some View {
        VStack(spacing: 0) {
            HStack {
                Text("Album Songs")
                    .font(.system(size: 40))
                    .fontWeight(.bold)
                    .foregroundColor(.black)

                Spacer(minLength: 0)
            }
            .padding()
            .padding(.top, UIApplication.shared.windows.first?.safeAreaInsets.top)
            .background(
                Color.white
                    .shadow(color: Color.black.opacity(0.18), radius: 5, x: 0, y: 5)
            )
            .zIndex(0)
            // Move view in stack for shadow effect

            // Scaling effect
            GeometryReader { mainView in
                ScrollView {
                    VStack(spacing: 15) {
                        // Set name as id
                        ForEach(albums, id: \.albumName) { album in
                            // Album View
                            GeometryReader { item in
                                SermonView(album: album)
                                    // Scaling effect from bottom
                                    .scaleEffect(scaleValue(mainFrame: mainView.frame(in: .global).minY, minY: item.frame(in: .global).minY), anchor: .bottom)
                                    // Add opacity effect
                                    .opacity(Double(scaleValue(mainFrame: mainView.frame(in: .global).minY, minY: item.frame(in: .global).minY)))

                            }
                            .frame(height: 100)
                        }
                    }
                    .padding(.horizontal)
                    .padding(.top, 25)
                }
                .zIndex(1)
            }
        }
        .background(Color.black.opacity(0.06).edgesIgnoringSafeArea(.all))
        .edgesIgnoringSafeArea(.top)
    }

    // Simple calculation for scaling effect
    func scaleValue(mainFrame: CGFloat, minY: CGFloat) -> CGFloat {
        // Add aniamtion
        withAnimation(.easeOut) {
            // Reduce top padding value
            let scale = (minY - 25) / mainFrame

            // Return scaling value to album view if its less than 1
            if scale > 1 {
                return 1
            } else {
                return scale
            }
        }
    }
}

struct Album {
    var albumName: String
    var albumAuthor: String
    var albumCover: String
}

var albums = [
    Album(albumName: "Let Her Go", albumAuthor: "Passenger", albumCover: "p1"),
    Album(albumName: "Bad Blood", albumAuthor: "Taylor Swift", albumCover: "p2"),
    Album(albumName: "Believer", albumAuthor: "Kurt Hugo Schneider", albumCover: "p3"),
    Album(albumName: "Let Me Love You", albumAuthor: "DJ Snake", albumCover: "p4"),
    Album(albumName: "Shape Of You", albumAuthor: "Ed Sherran", albumCover: "p5"),
    Album(albumName: "Blank Space", albumAuthor: "Taylor Swift", albumCover: "p6"),
    Album(albumName: "Havana", albumAuthor: "Camila Cabello", albumCover: "p7"),
    Album(albumName: "Red", albumAuthor: "Taylor Swift", albumCover: "p8"),
    Album(albumName: "I Like It", albumAuthor: "J Balvin", albumCover: "p9"),
    Album(albumName: "Lover", albumAuthor: "Taylor Swift", albumCover: "p10"),
    Album(albumName: "7/27 Harmony", albumAuthor: "Camila Cabello", albumCover: "p11"),
    Album(albumName: "Joanne", albumAuthor: "Lady Gaga", albumCover: "p12"),
    Album(albumName: "Roar", albumAuthor: "Katy Perry", albumCover: "p13"),
    Album(albumName: "My Church", albumAuthor: "Maren Morris", albumCover: "p14"),
    Album(albumName: "Part Of Me", albumAuthor: "Katy Perry", albumCover: "p15")
]

// Note: The code below was taken from the sample app from https://developer.apple.com/documentation/passkit/apple_pay/offering_apple_pay_in_your_app - shortened and adapted for this application

// Typealias so we don't always need to rewrite the type (Bool) -> Void
typealias PaymentCompletionHandler = (Bool) -> Void

class PaymentHandler: NSObject {
    
    var paymentController: PKPaymentAuthorizationController?
    var paymentSummaryItems = [PKPaymentSummaryItem]()
    var paymentStatus = PKPaymentAuthorizationStatus.failure
    var completionHandler: PaymentCompletionHandler?
    
    static let supportedNetworks: [PKPaymentNetwork] = [
        .visa,
        .masterCard,
        .discover
    ]
    
    // This applePayStatus function is not used in this app. Use it to check for the ability to make payments using canMakePayments(), and check for available payment cards using canMakePayments(usingNetworks:). You can also display a custom PaymentButton according to the result. See https://developer.apple.com/documentation/passkit/apple_pay/offering_apple_pay_in_your_app under "Add the Apple Pay Button" section
    class func applePayStatus() -> (canMakePayments: Bool, canSetupCards: Bool) {
        return (PKPaymentAuthorizationController.canMakePayments(),
                PKPaymentAuthorizationController.canMakePayments(usingNetworks: supportedNetworks))
    }
    
    // Define the shipping methods (this app only offers delivery) and the delivery dates
    func shippingMethodCalculator() -> [PKShippingMethod] {
        
        let today = Date()
        let calendar = Calendar.current
        
        let shippingStart = calendar.date(byAdding: .day, value: 5, to: today)
        let shippingEnd = calendar.date(byAdding: .day, value: 10, to: today)
        
        if let shippingEnd = shippingEnd, let shippingStart = shippingStart {
            let startComponents = calendar.dateComponents([.calendar, .year, .month, .day], from: shippingStart)
            let endComponents = calendar.dateComponents([.calendar, .year, .month, .day], from: shippingEnd)
            
            let shippingDelivery = PKShippingMethod(label: "Delivery", amount: NSDecimalNumber(string: "0.00"))
            shippingDelivery.dateComponentsRange = PKDateComponentsRange(start: startComponents, end: endComponents)
            shippingDelivery.detail = "Sweaters sent to your address"
            shippingDelivery.identifier = "DELIVERY"
            
            return [shippingDelivery]
        }
        return []
    }
    
    func startPayment(products: [Product], total: Int, completion: @escaping PaymentCompletionHandler) {
        completionHandler = completion
        
        // Iterate over the products array, create a PKPaymentSummaryItem for each and append to the paymentSummaryItems array
        products.forEach { product in
            let item = PKPaymentSummaryItem(label: product.name, amount: NSDecimalNumber(string: "\(product.price).00"), type: .final)
            paymentSummaryItems.append(item)
        }
        
        // Add a PKPaymentSummaryItem for the total to the paymentSummaryItems array
        let total = PKPaymentSummaryItem(label: "Total", amount: NSDecimalNumber(string: "\(total).00"), type: .final)
        paymentSummaryItems.append(total)
        
        // Create a payment request and add all data to it
        let paymentRequest = PKPaymentRequest()
        paymentRequest.paymentSummaryItems = paymentSummaryItems // Set paymentSummaryItems to the paymentRequest
        paymentRequest.merchantIdentifier = "merchant.io.designcode.sweatershopapp"
        paymentRequest.merchantCapabilities = .capability3DS // A security protocol used to authenticate users
        paymentRequest.countryCode = "US"
        paymentRequest.currencyCode = "USD"
        paymentRequest.supportedNetworks = PaymentHandler.supportedNetworks // Types of cards supported
        paymentRequest.shippingType = .delivery
        paymentRequest.shippingMethods = shippingMethodCalculator()
        paymentRequest.requiredShippingContactFields = [.name, .postalAddress]
        
        // Display the payment request in a sheet presentation
        paymentController = PKPaymentAuthorizationController(paymentRequest: paymentRequest)
        paymentController?.delegate = self
        paymentController?.present(completion: { (presented: Bool) in
            if presented {
                debugPrint("Presented payment controller")
            } else {
                debugPrint("Failed to present payment controller")
                if let completionHandler = self.completionHandler {
                    completionHandler(false)
                }
            }
        })
    }
}

// Set up PKPaymentAuthorizationControllerDelegate conformance
extension PaymentHandler: PKPaymentAuthorizationControllerDelegate {

    // Handle success and errors related to the payment
    func paymentAuthorizationController(_ controller: PKPaymentAuthorizationController, didAuthorizePayment payment: PKPayment, handler completion: @escaping (PKPaymentAuthorizationResult) -> Void) {

        let errors = [Error]()
        let status = PKPaymentAuthorizationStatus.success

        self.paymentStatus = status
        completion(PKPaymentAuthorizationResult(status: status, errors: errors))
    }

    func paymentAuthorizationControllerDidFinish(_ controller: PKPaymentAuthorizationController) {
        controller.dismiss {
            // The payment sheet doesn't automatically dismiss once it has finished, so dismiss the payment sheet
            DispatchQueue.main.async {
                if self.paymentStatus == .success {
                    if let completionHandler = self.completionHandler {
                        completionHandler(true)
                    }
                } else {
                    if let completionHandler = self.completionHandler {
                        completionHandler(false)
                    }
                }
            }
        }
    }
}

class CartManager: ObservableObject {
    @Published private(set) var products: [Product] = []
    @Published private(set) var total: Int = 0
    
    // Payment-related variables
    let paymentHandler = PaymentHandler()
    @Published var paymentSuccess = false
    
    // Functions to add and remove from cart
    func addToCart(product: Product) {
        products.append(product)
        total += product.price
    }
    
    func removeFromCart(product: Product) {
        products = products.filter { $0.id != product.id }
        total -= product.price
    }
    
    // Call the startPayment function from the PaymentHandler. In the completion handler, set the paymentSuccess variable
    func pay() {
        paymentHandler.startPayment(products: products, total: total) { success in
            self.paymentSuccess = success
            self.products = []
            self.total = 0
        }
    }
}

struct PaymentContentView: View {
    @StateObject var cartManager = CartManager()
    var columns = [GridItem(.adaptive(minimum: 160), spacing: 20)]
    
    var body: some View {
        NavigationView {
            ScrollView {
                LazyVGrid(columns: columns, spacing: 20) {
                    ForEach(productList, id: \.id) { product in
                        ProductCard(product: product)
                            .environmentObject(cartManager)
                    }
                }
                .padding()
            }
            .navigationTitle(Text("Shop"))
            .toolbar {
                NavigationLink {
                    CartView()
                        .environmentObject(cartManager)
                } label: {
                    CartButton(numberOfProducts: cartManager.products.count)
                }
            }
        }
        .navigationViewStyle(StackNavigationViewStyle())
    }
}

struct PaymentContentView_Previews: PreviewProvider {
    static var previews: some View {
        PaymentContentView()
    }
}

struct Product: Identifiable {
    var id = UUID()
    var name: String
    var image: String
    var price: Int
}

var productList = [
    Product(name: "Grace Upon Grace", image: "background1", price: 54),
    Product(name: "Faith That Moves Mountains", image: "background2", price: 89),
    Product(name: "Walking in His Light", image: "background3", price: 79),
    Product(name: "Unshakable Hope", image: "background4", price: 94),
    Product(name: "The Power of Prayer", image: "background5", price: 99),
    Product(name: "Living in God’s Favor", image: "background6", price: 65),
    Product(name: "Renewed by the Word", image: "background7", price: 54),
    Product(name: "Courageous Faith", image: "background8", price: 83),
]

struct ProductCard: View {
    @EnvironmentObject var cartManager: CartManager
    var product: Product
    
    var body: some View {
        ZStack(alignment: .topTrailing) {
            ZStack(alignment: .bottom) {
                Image(product.image)
                    .resizable()
                    .cornerRadius(20)
                    .frame(width: 180)
                    .scaledToFit()
                
                VStack(alignment: .leading) {
#warning("should use popovers to display this text in case it doesn't fit")
                    Text(product.name)
                        .bold()
                    
                    Text("\(product.price)$")
                        .font(.caption)
                }
                .padding()
                .frame(width: 180, height: 50, alignment: .leading)
                .background(.ultraThinMaterial)
                .cornerRadius(20)
            }
            .frame(width: 180, height: 250)
            .shadow(radius: 3)
            
            Button {
                cartManager.addToCart(product: product)
            } label: {
                Image(systemName: "plus")
                    .padding(10)
                    .foregroundColor(.white)
                    .background(.black)
                    .cornerRadius(50)
                    .padding()
            }
        }
    }
}

struct CartView: View {
    @EnvironmentObject var cartManager: CartManager
    
    var body: some View {
        ScrollView {
            if cartManager.paymentSuccess {
                Text("Thanks for your purchase! You'll get cozy in our comfy sweaters soon! You'll also receive an email confirmation shortly.")
                    .padding()
            } else {
                if cartManager.products.count > 0 {
                    ForEach(cartManager.products, id: \.id) { product in
                        ProductRow(product: product)
                    }
                    
                    HStack {
                        Text("Your cart total is")
                        Spacer()
                        Text("$\(cartManager.total).00")
                            .bold()
                    }
                    .padding()
                    
                    PaymentButton(action: cartManager.pay)
                        .padding()
                    
                } else {
                    Text("Your cart is empty.")
                }
            }
        }
        .navigationTitle(Text("My Cart"))
        .padding(.top)
        .onDisappear {
            if cartManager.paymentSuccess {
                cartManager.paymentSuccess = false
            }
        }
    }
}

struct CartButton: View {
    var numberOfProducts: Int
    
    var body: some View {
        ZStack(alignment: .topTrailing) {
            Image(systemName: "cart")
                .padding(.top, 5)

            if numberOfProducts > 0 {
                Text("\(numberOfProducts)")
                    .font(.caption2).bold()
                    .foregroundColor(.white)
                    .frame(width: 15, height: 15)
                    .background(Color(hue: 1.0, saturation: 0.89, brightness: 0.835))
                    .cornerRadius(50)
            }
        }
    }
}

struct ProductRow: View {
    @EnvironmentObject var cartManager: CartManager
    var product: Product
    
    var body: some View {
        HStack(spacing: 20) {
            Image(product.image)
                .resizable()
                .aspectRatio(contentMode: .fit)
                .frame(width: 50)
                .cornerRadius(10)
            
            VStack(alignment: .leading, spacing: 10) {
                Text(product.name)
                    .bold()

                Text("$\(product.price)")
            }
            
            Spacer()

            Image(systemName: "trash")
                .foregroundColor(Color(hue: 1.0, saturation: 0.89, brightness: 0.835))
                .onTapGesture {
                    cartManager.removeFromCart(product: product)
                }
        }
        .padding(.horizontal)
        .frame(maxWidth: .infinity, alignment: .leading)
    }
}

struct PaymentButton: View {
    var action: () -> Void
    
    var body: some View {
        Representable(action: action)
            .frame(minWidth: 100, maxWidth: 400)
            .frame(height: 45)
            .frame(maxWidth: .infinity)
    }
}

extension PaymentButton {
    struct Representable: UIViewRepresentable {
        var action: () -> Void
        
        func makeCoordinator() -> Coordinator {
            Coordinator(action: action)
        }
        
        func makeUIView(context: Context) -> UIView {
            context.coordinator.button
        }
        
        func updateUIView(_ rootView: UIView, context: Context) {
            context.coordinator.action = action
        }
    }
    
    class Coordinator: NSObject {
        var action: () -> Void
        var button = PKPaymentButton(paymentButtonType: .checkout, paymentButtonStyle: .automatic)
        
        init(action: @escaping () -> Void) {
            self.action = action
            super.init()

            button.addTarget(self, action: #selector(callback(_:)), for: .touchUpInside)
        }
        
        @objc
        func callback(_ sender: Any) {
            action()
        }
    }
}

struct Post: Identifiable, Codable, Equatable, Hashable {
    @DocumentID var id: String?
    var text: String
    var imageURL: URL?
    var imageReferenceID: String = "" //used for deletions
    var publishedDate : Date = Date()
    var upvoteIDs:[String] = []
    var downvoteIDs: [String] = []
    //Post Author details
    var userName: String
    var userUID: String
    //var userProfileURL: URL - not needed because we retrieve user avatars from avatar_urls in Firebase Firestore using the user ID
    
    enum CodingKeys: CodingKey {
        case id
        case text
        case imageURL
        case imageReferenceID //used for deletions
        case publishedDate
        case upvoteIDs
        case downvoteIDs
        case userName
        case userUID
        //case userProfileURL
    }
}

struct RoundedCorner: Shape {
    
    var radius: CGFloat = .infinity
    var corners: UIRectCorner = .allCorners
    
    func path(in rect: CGRect) -> Path {
        let path = UIBezierPath(roundedRect: rect, byRoundingCorners: corners, cornerRadii: CGSize(width: radius, height: radius))
        return Path(path.cgPath)
    }
}

extension View {
    func glow(gradient: LinearGradient, radius: CGFloat = 20) -> some View {
        self
        // 1. The Top "Bloom" (from your original code)
        // We mask the gradient to the view and blur it slightly on top
            .overlay(
                gradient
                    .mask(self)
                    .blur(radius: radius / 6)
                    .allowsHitTesting(false) // Ensure it doesn't block touches
            )
        // 2. The Background "Shadow"
        // Since .shadow() can't take a gradient, we use .background()
            .background(
                ZStack {
                    // Layer A: Intense inner glow
                    gradient
                        .mask(self)
                        .blur(radius: radius / 3)
                    
                    // Layer B: Duplicate to match the intensity of your original 3 shadows
                    gradient
                        .mask(self)
                        .blur(radius: radius / 3)
                        .opacity(0.8)
                }
            )
    }
}

extension View {
    ///using the custom RoundedCorner Shape to render custom rounding on a view
    func customCornerRadius(_ radius: CGFloat, corners: UIRectCorner) -> some View {
        clipShape( RoundedCorner(radius: radius, corners: corners) )
    }
    
    /// returns screen size
    func getRect() -> CGRect {
        return UIScreen.main.bounds
    }
    
    /// Returns screen size as CGSize
    func getScreenSize() -> CGSize {
        guard let window = UIApplication.shared.connectedScenes.first as? UIWindowScene else {
            return .zero
        }

        return window.screen.bounds.size
    }
    
    ///Safe Area Values
    func safeArea() -> UIEdgeInsets {
        guard let screen = UIApplication.shared.connectedScenes.first as? UIWindowScene else {
            return .zero
        }
        
        guard let safeArea = screen.windows.first?.safeAreaInsets else {
            return .zero
        }
        
        return safeArea
    }
    
    func glow(color: Color = .red, radius: CGFloat = 20) -> some View {
        self
            .overlay(self.blur(radius: radius / 6))
            .shadow(color: color, radius: radius / 3)
            .shadow(color: color, radius: radius / 3)
            .shadow(color: color, radius: radius / 3)
    }
    
    //Scrollview offset
    func offset(offset: Binding<CGFloat>) -> some View {
        return self
            .overlay {
                GeometryReader{ geometry in
                    let minY = geometry.frame(in: .named("SCROLL")).minY
                    
                    Color.clear
                        .preference(key: OffsetKey.self, value: minY)
                }
                .onPreferenceChange(OffsetKey.self) { value in
                    offset.wrappedValue = value
                }
            }
    }
    
    func animatableGradient(fromGradient: Gradient, toGradient: Gradient, progress: CGFloat) -> some View {
        self.modifier(AnimatableGradientModifier(fromGradient: fromGradient, toGradient: toGradient, progress: progress))
    }
    
    /// Applies the given transform if the given condition evaluates to `true`.
    /// - Parameters:
    ///   - condition: The condition to evaluate.
    ///   - transform: The transform to apply to the source `View`.
    /// - Returns: Either the original `View` or the modified `View` if the condition is `true`.
    @ViewBuilder func `if`<Content: View>(_ condition: Bool, transform: (Self) -> Content) -> some View {
        if condition {
            transform(self)
        } else {
            self
        }
    }
    
    /// Applies one of two transforms based on a condition.
    /// - Parameters:
    ///   - condition: The condition to evaluate.
    ///   - ifTransform: The transform to apply when the condition is `true`.
    ///   - elseTransform: The transform to apply when the condition is `false`.
    /// - Returns: Either the `ifTransform` result or the `elseTransform` result based on the condition.
    @ViewBuilder func `if`<TrueContent: View, FalseContent: View>(
        _ condition: Bool,
        transform: (Self) -> TrueContent,
        else elseTransform: (Self) -> FalseContent
    ) -> some View {
        if condition {
            transform(self)
        } else {
            elseTransform(self)
        }
    }
    
    /// Performs work based on the value of a boolean argument.
    ///
    /// - Parameters:
    ///   - condition: The boolean value determining whether to perform the work.
    ///   - work: The closure containing the work to be performed.
    /// - Returns: A modified version of the view.
    func performWorkIf(_ condition: Bool, _ work: () -> Void) {
        if condition {
            work()
        }
    }
}

extension View {
    /// Closes all active keyboards
    func closeKeyboard () {
        UIApplication.shared.sendAction(#selector(UIResponder.resignFirstResponder), to: nil, from: nil, for: nil)
    }
    
    ///combines the functionality of disabled and opacity methods to depend on a single Boolean condition
    func disableWithOpacity(_ condition : Bool) -> some View {
        self
            .disabled(condition)
            .opacity(condition ? 0.4 : 1)
    }
    
    
    func horizontalAlign(_ alignment : Alignment) -> some View {
        self
            .frame(maxWidth: .infinity, alignment: alignment)
    }
    
    func verticalAlign(_ alignment : Alignment) -> some View {
        self
            .frame(maxHeight: .infinity, alignment: alignment)
    }
    
    ///Custom Border
    func paddedBorder(_ color: Color, _ linewidth : CGFloat) -> some View {
        self
            .padding(.horizontal, .large)
            .padding(.vertical, .medium)
            .background (
                RoundedRectangle(cornerRadius: 5, style: .continuous)
                    .stroke(color, lineWidth: linewidth)
            )
    }
    
    #warning("use chatgpt to create descriptors for all methods here")
    ///Custom Filling View. Will apply standard padding to view and fill with selected colo
    func fillView(_ color: Color) -> some View {
        self
            .padding(.horizontal, .large)
            .padding(.vertical, .medium)
            .background (
                RoundedRectangle(cornerRadius: 5, style: .continuous)
                    .fill(color)
            )
    }
    
    ///Textfield color and style changer API
    ///Use a custom placeholder modifier to show any view as the holder of any other view!
    func placeholder<Content: View>( when shouldShow: Bool, systemImageName: String = "", alignment: Alignment = .leading, @ViewBuilder placeholder: () -> Content) -> some View {
        ZStack(alignment: alignment) {
            HStack {
                placeholder().opacity(shouldShow ? 1 : 0)
                Spacer()
                Image(systemName: systemImageName).renderingMode(.original).opacity(shouldShow ? 1 : 0)
                TextfieldIcon(iconName: systemImageName, passedImage: .constant(nil), currentlyEditing: .constant(false)).opacity(shouldShow ? 1 : 0)
            }
            self
        }
    }
}

class UIBackdropView: UIView {
    override class var layerClass: AnyClass {
        NSClassFromString("CABackdropLayer") ?? CALayer.self
    }
}

struct Backdrop: UIViewRepresentable {
    func makeUIView(context: Context) -> UIBackdropView {
        UIBackdropView()
    }
    
    func updateUIView(_ uiView: UIBackdropView, context: Context) {}
}

struct Blur: View {
    var radius: CGFloat = 3
    var opaque: Bool = false
    
    var body: some View {
        Backdrop()
            .blur(radius: radius, opaque: opaque)
    }
}

extension View {
    func backgroundBlur(radius: CGFloat = 3, opaque: Bool = false) -> some View {
        self
            .background(
                Blur(radius: radius, opaque: opaque)
            )
    }
}

extension View {
    func innerShadow<S: Shape, SS: ShapeStyle>(shape: S, color: SS, lineWidth: CGFloat = 1, offsetX: CGFloat = 0, offsetY: CGFloat = 0, blur: CGFloat = 4, blendMode: BlendMode = .normal, opacity: Double = 1) -> some View {
        return self
            .overlay {
                shape
                    .stroke(color, lineWidth: lineWidth)
                    .blendMode(blendMode)
                    .offset(x: offsetX, y: offsetY)
                    .blur(radius: blur)
                    .mask(shape)
                    .opacity(opacity)
            }
    }
}

extension Image {
    
    /// Resize an image with fill aspect ratio and specified frame dimensions.
    ///   - parameters:
    ///     - width: Frame width.
    ///     - height: Frame height.
    func resizedToFill(width: CGFloat, height: CGFloat) -> some View {
        self
            .resizable()
            .aspectRatio(contentMode: .fill)
            .frame(width: width, height: height)
    }
    
    /// Resize an image with fit aspect ratio and specified frame dimensions.
    ///   - parameters:
    ///     - width: Frame width.
    ///     - height: Frame height.
    func resizedToFit(width: CGFloat, height: CGFloat) -> some View {
        self
            .resizable()
            .aspectRatio(contentMode: .fit)
            .frame(width: width, height: height)
    }
    
    /// Resize an image with fill aspect ratio, customizable aspect ratio value (default: 4/3) and specified frame dimensions.
    ///   - parameters:
    ///     - width: Frame width.
    ///     - height: Frame height.
    ///     - aspectRatio: value to pass to aspectRatio, if any
    func resizedToFillAspectRatio(width: CGFloat, height: CGFloat, aspectRatio: CGFloat = 4/3) -> some View {
        self
            .resizable()
            .aspectRatio(aspectRatio, contentMode: .fill)
            .frame(width: width, height: height)
    }
    
    /// Resize an image with fit aspect ratio, customizable aspect ratio value (default: 4/3) and specified frame dimensions.
    ///   - parameters:
    ///     - width: Frame width.
    ///     - height: Frame height.
    ///     - aspectRatio: value to pass to aspectRatio, if any
    func resizedToFitAspectRatio(width: CGFloat, height: CGFloat, aspectRatio: CGFloat = 4/3) -> some View {
        self
            .resizable()
            .aspectRatio(aspectRatio, contentMode: .fit)
            .frame(width: width, height: height)
    }
}


struct GradientText: View {
    var text: String = "Text here"
    var boldFontModifiersEnabled: Bool = false
    var gradientColors: [Color]? = nil
    var fontSize: CGFloat? = nil
    var lineLimit: Int? = nil
    var multiLineTextAlignment: TextAlignment = .center
    
    var body: some View {
        Text(text)
            .if(boldFontModifiersEnabled, transform: { thisView in
                thisView
                    .font(.system(size: fontSize ?? 30, weight: .bold))
                    .fontWeight(.bold)
                    .multilineTextAlignment(multiLineTextAlignment)
            })
            .if(lineLimit != nil, transform: { thisView in
                thisView
                    .multilineTextAlignment(multiLineTextAlignment)
                    .lineLimit(lineLimit!)
            })
            .gradientForeground(colors: gradientColors ?? [Color( colorLiteral(red: 0.6196078431, green: 0.6784313725, blue: 1, alpha: 1)), Color( colorLiteral(red: 1, green: 0.5607843137, blue: 0.9803921569, alpha: 1))])
    }
}

extension View {
    public func gradientForeground(colors: [Color] = [Color( colorLiteral(red: 0.6196078431, green: 0.6784313725, blue: 1, alpha: 1)), Color( colorLiteral(red: 1, green: 0.5607843137, blue: 0.9803921569, alpha: 1))]) -> some View {
        self.overlay(LinearGradient(gradient: .init(colors: colors), startPoint: .topLeading, endPoint: .bottomTrailing))
        .mask(self)
    }
}

struct SplashView: View {
    @State private var innerGap = true
    let streamBlue = Color( colorLiteral(red: 0, green: 0.3725490196, blue: 1, alpha: 1))
    
    var body: some View {
        ZStack {
            ForEach(0..<8, id: \.self) {
                Circle()
                    .foregroundStyle(
                        .linearGradient(
                            colors: [.green, .red],
                            startPoint: .bottom,
                            endPoint: .leading
                        )
                    )
                    .frame(width: 3, height: 3)
                    .offset(x: innerGap ? 24 : 0)
                    .rotationEffect(.degrees(Double($0) * 45))
                    .hueRotation(.degrees(300))
            }
            
            ForEach(0..<8, id: \.self) {
                Circle()
                    .foregroundStyle(
                        .linearGradient(
                            colors: [.green, streamBlue],
                            startPoint: .bottom,
                            endPoint: .leading
                        )
                    )
                    .frame(width: 4, height: 4)
                    .offset(x: innerGap ? 26 : 0)
                    .rotationEffect(.degrees(Double($0) * 45))
                    .hueRotation(.degrees(60))
                
            }
            .rotationEffect(.degrees(12))
        }
    }
}

extension View {
    func blurBackground() -> some View {
        self
            .padding(.small)
            .background(Color("Background 3"))
            .background(VisualEffectBlur(blurStyle: .systemUltraThinMaterialDark))
            .overlay(RoundedRectangle(cornerRadius: 20, style: .continuous)
                        .stroke(Color( colorLiteral(red: 1, green: 1, blue: 1, alpha: 1)), lineWidth: 1).blendMode(.overlay))
            .mask(RoundedRectangle(cornerRadius: 20, style: .continuous))
    }
    
    func angularGradientGlow(colors: [Color]) -> some View {
        self.overlay(AngularGradient(gradient: Gradient(colors: colors), center: .center, angle: .degrees(0.0)))
            .mask(self)
    }
    
    func linearGradientBackground(colors: [Color]) -> some View {
        self.overlay(LinearGradient(gradient: .init(colors: colors),
                                    startPoint: .topLeading,
                                    endPoint: .bottomTrailing))
            .mask(self)
    }
    
}

struct VisualEffectBlur<Content: View>: View {
    var blurStyle: UIBlurEffect.Style
    var vibrancyStyle: UIVibrancyEffectStyle?
    var content: Content

    init(blurStyle: UIBlurEffect.Style = .systemMaterial, vibrancyStyle: UIVibrancyEffectStyle? = nil, @ViewBuilder content: () -> Content) {
        self.blurStyle = blurStyle
        self.vibrancyStyle = vibrancyStyle
        self.content = content()
    }

    var body: some View {
        Representable(blurStyle: blurStyle, vibrancyStyle: vibrancyStyle, content: ZStack { content })
            .accessibility(hidden: Content.self == EmptyView.self)
    }
}

extension VisualEffectBlur {
    struct Representable<Content: View>: UIViewRepresentable {
        var blurStyle: UIBlurEffect.Style
        var vibrancyStyle: UIVibrancyEffectStyle?
        var content: Content

        func makeUIView(context: Context) -> UIVisualEffectView {
            context.coordinator.blurView
        }

        func updateUIView(_ view: UIVisualEffectView, context: Context) {
            context.coordinator.update(content: content, blurStyle: blurStyle, vibrancyStyle: vibrancyStyle)
        }

        func makeCoordinator() -> Coordinator {
            Coordinator(content: content)
        }
    }
}

extension VisualEffectBlur.Representable {
    class Coordinator {
        let blurView = UIVisualEffectView()
        let vibrancyView = UIVisualEffectView()
        let hostingController: UIHostingController<Content>

        init(content: Content) {
            hostingController = UIHostingController(rootView: content)
            hostingController.view.autoresizingMask = [.flexibleWidth, .flexibleHeight]
            hostingController.view.backgroundColor = nil
            blurView.contentView.addSubview(vibrancyView)
            blurView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
            vibrancyView.contentView.addSubview(hostingController.view)
            vibrancyView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
        }

        func update(content: Content, blurStyle: UIBlurEffect.Style, vibrancyStyle: UIVibrancyEffectStyle?) {
            hostingController.rootView = content
            let blurEffect = UIBlurEffect(style: blurStyle)
            blurView.effect = blurEffect
            if let vibrancyStyle = vibrancyStyle {
                vibrancyView.effect = UIVibrancyEffect(blurEffect: blurEffect, style: vibrancyStyle)
            } else {
                vibrancyView.effect = nil
            }
            hostingController.view.setNeedsDisplay()
        }
    }
}

extension VisualEffectBlur where Content == EmptyView {
    init(blurStyle: UIBlurEffect.Style = .systemMaterial) {
        self.init(blurStyle: blurStyle, vibrancyStyle: nil) {
            EmptyView()
        }
    }
}

//struct VisualEffectBlur_Previews: PreviewProvider {
//    static var previews: some View {
//        ZStack {
//            LinearGradient(
//                gradient: Gradient(colors: [.red, .blue]),
//                startPoint: .topLeading,
//                endPoint: .bottomTrailing
//            )
//
            #warning("might be interested in the aestehtic of this text")
//            VisualEffectBlur(blurStyle: .systemUltraThinMaterial, vibrancyStyle: .fill) {
//                Text("Hello World!")
//                    .frame(width: 200, height: 100)
//            }
//        }
//    }
//}

struct PhotoPicker: UIViewControllerRepresentable {
    //@Binding var show: Bool
    //@EnvironmentObject var registerVM: RegisterViewModel //no longer uploading images to Firebase
    //@Binding var imageData: Data?
    @Binding var avatar: UIImage?
    
    func makeUIViewController(context: Context) -> UIImagePickerController {
        let picker = UIImagePickerController()
        picker.delegate = context.coordinator
        picker.allowsEditing = true
        return picker
    }
    
    func updateUIViewController(_ uiViewController: UIImagePickerController, context: Context) {
        //not needed in our case
    }
    
    func makeCoordinator() -> Coordinator {
        return Coordinator(photoPicker: self)
    }

    
    final class Coordinator: NSObject, UINavigationControllerDelegate, UIImagePickerControllerDelegate {
        //UIImagePickerControllerDelegate is what fires off when user selects an image
        //UINavigationControllerDelegate necessary so we get access to a dismiss button
        
        let photoPicker: PhotoPicker
        
        init(photoPicker: PhotoPicker) {
            self.photoPicker = photoPicker
        }
        
        func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
            if let image = info[.editedImage] as? UIImage {
                guard let data = image.jpegData(compressionQuality: 0.2), let compressedImage = UIImage(data: data) else {
                    Task {
                        await setErrorWithMessage("Error", PhotoPickerError.compressionError, handler: {})
                    }
                    return
                }
                photoPicker.avatar = compressedImage
#warning("should fix this and not save photos in user defaults in future")
                UserDefaults.standard.set(data, forKey: "user_Image")
            } else {
                Task {
                    await setErrorWithMessage("Error", PhotoPickerError.initializationError, handler: {})
                }
            }
            picker.dismiss(animated: true)
        }
    }
}

struct LoadingView: View {
    @Binding var show : Bool
    @State private var animate: Bool = false
    @State private var color: Color = .gray
    @State private var pattern: [[Int]] = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8,],
        [9, 10, 11, 12, 13, 14, 15, 16, 17,],
        [18, 19, 20, 21, 22, 23, 24, 24, 26,],
        [27, 28, 29, 30, 31, 32, 33, 34, 35,],
        [36, 37, 38, 39, 40, 41, 42, 43, 44,],
        [45, 46, 47, 48, 49, 50, 51, 52, 53,],
        [54, 55, 56, 57, 58, 59, 60, 61, 62,],
        [63, 64, 65, 66, 67, 68, 69, 70, 71,],
        [72, 73, 74, 75, 76, 77, 78, 79, 80,],
    ]


    let colors: [Color] = [.blue, .green, .yellow, .orange]

    var body: some View {
        if show {
            VStack(spacing: 2) {
                ForEach(0..<9, id: \.self) { i in
                    HStack(spacing: 2) {
                        ForEach(0..<9, id: \.self) { j in
                            RoundedRectangle(cornerRadius: 12)
                                .foregroundStyle(color)
                                .aspectRatio(1, contentMode: .fit)
                                .scaleEffect(animate ? 1 : 0)
                                .opacity(animate ? 1 : 0)
                                .rotationEffect(.degrees(animate ? 360 : 0))
                                .animation(.easeInOut(duration: 0.1).delay(delay(row: i, col: j)), value: animate)
                        }
                    }
                }
            }
            .padding()
            .onAppear {
                Timer.scheduledTimer(withTimeInterval: 1.5, repeats: true) { _ in
                    if !animate { color = colors.randomElement()! }
                    animate.toggle()
                }
            }
            .animation(.easeInOut(duration: 0.7), value: show)
        }
    }

    func delay(row: Int, col: Int) -> Double {
        if !pattern.isEmpty {
            let delay = pattern[row][col]
            return Double(delay) * (1.0 / 80.0)
        }
        return 0.0
    }
}

struct CustomProgressView: View {
    var body: some View {
        ZStack {
            Group {
                Rectangle()
                    .fill(Color.black.opacity(0.3))
                    .ignoresSafeArea()
                    .cornerRadius(10)
                
                ProgressView()
                    .padding(15)
                    .background(Color.blue, in: RoundedRectangle(cornerRadius: 10, style: .continuous))
            }
            .background(
                Color("settingsBackground")
                    .edgesIgnoringSafeArea(.all)
            )
        }
    }
}

struct GradientIcon: View {
    var iconName: String
    var frameWidth: CGFloat? = nil
    var frameHeight: CGFloat? = nil
    var colors: [Color] = [
        Color( colorLiteral(red: 0.6196078431, green: 0.6784313725, blue: 1, alpha: 1)), Color( colorLiteral(red: 1, green: 0.5607843137, blue: 0.9803921569, alpha: 1))
    ]

    var body: some View {
        LinearGradient(
            gradient: Gradient(colors: colors),
            startPoint: .topLeading,
            endPoint: .bottomTrailing
        )
        .mask(
            Image(systemName: iconName)
                .resizable()
                .scaledToFit()
                .foregroundColor(.white)
        )
        .if((frameHeight != nil) || (frameWidth != nil), transform: { thisView in
            thisView
                .frame(width: frameWidth!, height: frameHeight!)
        }, else: { thisView in
            thisView
                .frame(width: 20, height: 20)
        })
    }
}

struct OffsetModifier: ViewModifier {
    
    @Binding var offset: CGFloat
    
    //Option to return value from 0
    var returnFromStart: Bool = true
    @State var startValue: CGFloat = 0
    
    func body(content: Content) -> some View {
        content
            .overlay {
                GeometryReader { geometry in
                    Color.clear
                        .preference(key: OffsetKey.self, value: geometry.frame(in: .named("SCROLL")).minY)
                        .onPreferenceChange(OffsetKey.self) { value in
                            if startValue == 0 {
                                startValue = value
                            }
                            offset = (value - (returnFromStart ? startValue : 0))
                        }
                }
            }
    }
}

//Preference key
struct OffsetKey: PreferenceKey {
    static var defaultValue: CGFloat = 0
    
    static func reduce(value: inout CGFloat, nextValue: () -> CGFloat) {
        value = nextValue()
    }
}

public extension CGFloat {
    /// 4
    static let small: CGFloat = 4
    /// 8
    static let medium: CGFloat = 8
    /// 16
    static let large: CGFloat = 16
    /// 24
    static let xLarge: CGFloat = 24
    /// 32
    static let xxLarge: CGFloat = 34
    /// 42
    static let xxxLarge: CGFloat = 42
}

struct AnimatableGradientModifier: AnimatableModifier {
    let fromGradient: Gradient
    let toGradient: Gradient
    var progress: CGFloat = 0.0

    var animatableData: CGFloat {
        get { progress }
        set { progress = newValue }
    }

    func body(content: Content) -> some View {
        var gradientColors = [Color]()

        for i in 0..<fromGradient.stops.count {
            let fromColor = UIColor(fromGradient.stops[i].color)
            let toColor = UIColor(toGradient.stops[i].color)

            gradientColors.append(colorMixer(fromColor: fromColor, toColor: toColor, progress: progress))
        }

        return LinearGradient(gradient: Gradient(colors: gradientColors), startPoint: .topLeading, endPoint: .bottomTrailing)
    }

    func colorMixer(fromColor: UIColor, toColor: UIColor, progress: CGFloat) -> Color {
        guard let fromColor = fromColor.cgColor.components else { return Color(fromColor) }
        guard let toColor = toColor.cgColor.components else { return Color(toColor) }

        let red = fromColor[0] + (toColor[0] - fromColor[0]) * progress
        let green = fromColor[1] + (toColor[1] - fromColor[1]) * progress
        let blue = fromColor[2] + (toColor[2] - fromColor[2]) * progress

        return Color(red: Double(red), green: Double(green), blue: Double(blue))
    }
}

struct TextfieldIcon: View {
    var iconName: String
    @Binding var passedImage: UIImage?
    @Binding var currentlyEditing: Bool
    @State private var colorAngle = 180.0

    var body: some View {
        ZStack {
            VisualEffectBlur(blurStyle: .dark) {
                ZStack {
                    if currentlyEditing {
                        AngularGradient(gradient: Gradient(colors: [
                            Color(red: 101/255, green: 134/255, blue: 1),
                            Color(red: 1, green: 64/255, blue: 80/255),
                            Color(red: 109/255, green: 1, blue: 185/255),
                            Color(red: 39/255, green: 232/255, blue: 1),
                        ]), center: .center, angle: .degrees(colorAngle))
                        .blur(radius: 10)
                        .onAppear {
                            withAnimation(.linear(duration: 7)) {
                                self.colorAngle += 350
                            }
                        }
                    }
                }
                Color("tertiaryBackground")
                    .cornerRadius(12)
                    .opacity(0.8)
                    .blur(radius: 3.0)
            }
            .cornerRadius(12)
            .overlay(
                ZStack {
                    RoundedRectangle(cornerRadius: 12, style: .circular)
                        .stroke(Color.white, lineWidth: 1)
                        .blendMode(.overlay)
                    if passedImage != nil {
                        Image(uiImage: passedImage!)
                            .resizable()
                            .aspectRatio(contentMode: .fill)
                            .frame(width: passedImage == nil ? .large : .xxxLarge, height: passedImage == nil ? .large : .xxxLarge)
                            .cornerRadius(8)
                    } else {
                        GradientIcon(iconName: iconName)
                    }
                }
            )
        }
        .frame(width: .xLarge, height: .xLarge)
        .padding([.vertical, .leading], 8)
    }
}

func showAutoDismissingAlert(_ alertTitle: String, _ alertMessage: String, duration: TimeInterval = 2.0, completion: (() -> Void)? = nil) {
    DispatchQueue.main.async {
        let alertView = UIAlertController(title: alertTitle, message: alertMessage, preferredStyle: .alert)
        
        // Function to dismiss the alert
        func dismissAlert() {
            alertView.dismiss(animated: true, completion: completion)
        }
        
        // Present the alert
        if let windowScene = UIApplication.shared.connectedScenes.first as? UIWindowScene,
           let window = windowScene.windows.first,
           let rootVC = window.rootViewController {
            rootVC.present(alertView, animated: true) {
                // Set up the auto-dismissal
                DispatchQueue.main.asyncAfter(deadline: .now() + duration) {
                    dismissAlert()
                }
            }
        } else {
            // Fallback to key window if the above fails
            if let keyWindow = UIApplication.shared.connectedScenes
                .compactMap({ $0 as? UIWindowScene })
                .first(where: { $0.activationState == .foregroundActive })?.windows
                .first(where: { $0.isKeyWindow }) {
                
                keyWindow.rootViewController?.present(alertView, animated: true) {
                    // Set up the auto-dismissal
                    DispatchQueue.main.asyncAfter(deadline: .now() + duration) {
                        dismissAlert()
                    }
                }
            } else {
                print("Failed to present auto-dismissing alert")
                completion?()
            }
        }
    }
}


func setErrorWithMessage(_ errorMessage: String, _ error: Error, handler: @escaping () -> Void, failureHandler : (() -> Void)? = nil) async {
    //UI must be updated on main thread
    await MainActor.run(body: {
        showErrorAlertView(errorMessage, error.localizedDescription, handler: handler, failureHandler: failureHandler)
    })
}

func showErrorAlertView (_ alertTitle: String, _ alertMessage: String, handler: @escaping () -> Void, failureHandler : (() -> Void)? = nil) {
    //should find a way to use failure handler in future
    //handler should handle when user opts to continue despite the error
    //failurehandler should handle when user wants to rectify error, like a retry calling function or going back to finish exam
    //right now this method only handles continue situations
    DispatchQueue.main.async {
        let alertView = UIAlertController(title: alertTitle, message: alertMessage, preferredStyle: .alert)
        let ok = UIAlertAction(title: "Continue", style: .cancel) { _ in handler() }
        
        alertView.addAction(ok)
        
        //Presenting
        let scenes = UIApplication.shared.connectedScenes
        let windowScene = scenes.first as? UIWindowScene
        let window = windowScene?.windows.first
        let rootVC = window?.rootViewController
        rootVC?.present(alertView, animated: true)
        
        if (windowScene == nil) || (window == nil) || rootVC == nil {
            /// Handle failure to present alert view
            if let keyWindow = UIApplication.shared.connectedScenes
                .compactMap({ $0 as? UIWindowScene })
                .first(where: { $0.activationState == .foregroundActive })?.windows
                .first(where: { $0.isKeyWindow }) {
                
                let alertController = UIAlertController(title: "Key Window - \(alertTitle)", message: alertMessage, preferredStyle: .alert)
                alertController.addAction(UIAlertAction(title: "OK", style: .default) { _ in handler() })
                keyWindow.rootViewController?.present(alertController, animated: true, completion: nil)
            } else {
                /// If unable to get the key window, present the error message in the console
                print("Failed to present alert view")
            }
        }
    }
}

enum PhotoPickerError: Error {
    case compressionError
    case initializationError

    var localizedDescription: String {
        switch self {
        case .compressionError:
            return "Image Picker Controller compression error"
        case .initializationError:
            return  "Unable to initialize info.editedImage into a usable data stream"
        }
    }
}

extension LinearGradient {
    //named initializer to differentiate from built in (_color:) initializer
    init(mycolors: Color...){
        self.init(gradient: Gradient(colors: mycolors), startPoint: .topLeading, endPoint: .bottomTrailing)
    }
}

struct GradientButton: View {
    var buttonTitle: String
    var width: CGFloat?
    var height: CGFloat = 50
    var buttonAction: () -> Void
    var gradient1: [Color] = [
        Color(red: 101/255, green: 134/255, blue: 1),
        Color(red: 1, green: 64/255, blue: 80/255),
        Color(red: 109/255, green: 1, blue: 185/255),
        Color(red: 39/255, green: 232/255, blue: 1)
    ]
    @State private var angle: Double = 0
    
    var body: some View {
        Button(action: buttonAction) {
            GeometryReader { geometry in
                ZStack {
                    AngularGradient(gradient: Gradient(colors: gradient1), center: .center, angle: .degrees(angle))
                        .blendMode(.overlay)
                        .blur(radius: 8.0)
                        .mask (
                            RoundedRectangle(cornerRadius: 16)
                                .frame(maxWidth: width ?? geometry.size.width * 0.7)
                                .frame(height: height)
                                .blur(radius: 8)
                        )
                        .onAppear {
                            withAnimation(.linear(duration: 7)) {
                                self.angle += 350
                            }
                        }
                    GradientText(text: buttonTitle)
                        .font(.headline)
                        .frame(maxWidth: width ?? geometry.size.width * 0.7)
                        .frame(height: height)
                        .background(Color("tertiaryBackground").opacity(0.9))
                        .overlay(
                            RoundedRectangle(cornerRadius: 16.0)
                                .stroke(Color.white, lineWidth: 1.0)
                                .blendMode(.normal)
                                .opacity(0.7)
                        )
                        .cornerRadius(16.0)
                }
            }
        }
        .frame(height: height)
    }
}

extension View {
        /// Round view with specific corners
        /// - Parameters:
        ///   - radius: radius
        ///   - corners: corners to round
        /// - Returns: new rounded view
    public func cornerRadius(_ radius: CGFloat, corners: UIRectCorner) -> some View {
        clipShape(RoundedCorner(radius: radius, corners: corners))
    }
}

@available(iOS 15.0, macOS 12.0, tvOS 15.0, watchOS 8.0, *)
public struct LiquidGlassMaterial: View {
    public enum GradientStyle {
        case normal
        case reverted
    }
    
    let material: Material
    let materialOpacity: Double
    let color: Color
    let gradientOpacity: Double
    let gradientStyle: GradientStyle
    
    public init(
        material: Material = .ultraThinMaterial,
        materialOpacity: Double = 0.7,
        color: Color = .white,
        gradientOpacity: Double = 0.5,
        gradientStyle: GradientStyle = .normal
    ) {
        self.material = material
        self.materialOpacity = materialOpacity
        self.color = color
        self.gradientOpacity = gradientOpacity
        self.gradientStyle = gradientStyle
    }
    
    public var body: some View {
        ZStack {
            Rectangle()
                .fill(material)
                .opacity(materialOpacity)
            
            LinearGradient(
                gradient: Gradient(colors: gradientColors()),
                startPoint: .topLeading,
                endPoint: .bottomTrailing
            )
        }
    }
    
    private func gradientColors() -> [Color] {
        switch gradientStyle {
        case .normal:
            return [
                color.opacity(gradientOpacity),
                .clear,
                .clear,
                color.opacity(gradientOpacity)
            ]
        case .reverted:
            return [
                .clear,
                color.opacity(gradientOpacity),
                color.opacity(gradientOpacity),
                .clear
            ]
        }
    }
}

@available(iOS 15.0, macOS 12.0, tvOS 15.0, watchOS 8.0, *)
public extension View {
        /// Applies a liquid glass material effect to the background of a view.
        ///
        /// - Parameters:
        ///   - material: The base material for the blur effect.
        ///   - materialOpacity: The opacity of the base material.
        ///   - color: The color used for the gradient highlights.
        ///   - gradientOpacity: The opacity of the gradient highlights.
        ///   - gradientStyle: The style of the gradient.
        /// - Returns: A view with the liquid glass material effect applied to its background.
    func liquidGlass(
        material: Material = .ultraThinMaterial,
        materialOpacity: Double = 0.7,
        color: Color = .white,
        gradientOpacity: Double = 0.5,
        gradientStyle: LiquidGlassMaterial.GradientStyle = .normal
    ) -> some View {
        self.background(
            LiquidGlassMaterial(
                material: material,
                materialOpacity: materialOpacity,
                color: color,
                gradientOpacity: gradientOpacity,
                gradientStyle: gradientStyle
            )
        )
    }
}

struct Example2: View {
    @State private var animate: Bool = false
    var body: some View {
        List {
            let textShape = TextToShape(value: "New Life", font: textFont)
            
            Section("Demo") {
                textShape
                    .trim(from: 0, to: animate ? 1 : 0)
                    .stroke(lineWidth: 4)
                    .frame(height: 100)
            }
            
            Button("Animate") {
                withAnimation(.easeInOut(duration: 5)) {
                    animate.toggle()
                }
            }
        }
        .navigationTitle("Write Effect")
    }
    
    var textFont: UIFont {
        //native fon which is somewhat similar to handwriting
        if let customFont = UIFont(name: "Bradley Hand", size: 60) {
            return customFont
        }
        
        return .systemFont(ofSize: 40, weight: .light)
    }
}

struct TextToShape: Shape {
    var value: String
    var font: UIFont
    nonisolated func path(in rect: CGRect) -> Path {
        var path = Path()
        font.drawGlyphs(value) { position, glyphPath in
            let transform = CGAffineTransform(translationX: position.x, y: position.y).scaledBy(x: 1, y: -1)
            let newPath = Path(glyphPath).applying(transform)
            //adding it to the main path
            path.addPath(newPath)
        }
        
        //centering to current bounds
        let bounds = path.boundingRect
        let offsetX = rect.midX - bounds.midX
        let offsetY = rect.midY - bounds.midY
        let centerTransform = CGAffineTransform(translationX: offsetX, y: offsetY)
        
        return path.applying(centerTransform)
        
    }
}

extension UIFont {
    nonisolated var ctFont: CTFont { //converting UIFont into CTFont
        let descriptor = self.fontDescriptor
        return CTFontCreateWithFontDescriptor(descriptor, 0, nil)
    }
    
    //converting font into a NSAttributedString with the given value
    nonisolated func toNSAttributedString(_ value: String) -> NSAttributedString {
        return NSAttributedString(string: value, attributes: [.font : self])
    }
    
    //calculating textsize for the given font
    func toSize(_ value: String) -> CGSize {
        return NSString(string: value).size(withAttributes: [.font : self])
    }
    
    ///return each individual glyph path from the given text using the current font(can be used to draw text as path)
    nonisolated func drawGlyphs(_ value: String, draw: @escaping (_ position: CGPoint, _ glyphPath: CGPath) -> ()) {
        let ctFont = self.ctFont
        let attributedString = self.toNSAttributedString(value)
        
        //extracting lines & runs from the Attributed string using coretext APIs
        let lines = CTLineCreateWithAttributedString(attributedString)
        let runs = CTLineGetGlyphRuns(lines)
        
        for runIndex in 0..<CFArrayGetCount(runs) {
            let run = unsafeBitCast(CFArrayGetValueAtIndex(runs, runIndex), to: CTRun.self)
            let runCount = CTRunGetGlyphCount(run)
            
            //iterating run and frawing each glyph
            for index in 0..<runCount {
                let range = CFRangeMake(index, 1)
                var glyph = CGGlyph()
                var position = CGPoint()
                
                //extracting values
                CTRunGetGlyphs(run, range, &glyph)
                CTRunGetPositions(run, range, &position)
                
                if let glyphPath = CTFontCreatePathForGlyph(ctFont, glyph, nil) {
                    //passing to draw
                    draw(position, glyphPath)
                }
                
            }
        }
    }
}

//MARK: ---------------------------------------------------------------------------------------------Onboarding

@available(iOS 18.0, *)
struct AppleIntroOnboardingView: View {
    var action: () -> Void
    var body: some View {
        AppleIntroPage(action: action)
            .preferredColorScheme(.dark)
    }
}

struct Card: Identifiable, Hashable {
    var id: String = UUID().uuidString
    var image: String
}

let cards = [
    Card(image: "530225443_18519237487022446_742246057855281149_n"),
    Card(image: "530434985_18519237643022446_2023240685226568439_n"),
    Card(image: "609552045_18548231323022446_7800626355055026685_n"),
    Card(image: "609744752_18548231296022446_3317648494313211994_n"),
    Card(image: "544331948_18524436511022446_2115806341263360458_n"),
]

extension View {
    func blurOpacityEffect(_ show: Bool) -> some View {
        self
            .blur(radius: show ? 0 : 2)
            .opacity(show ? 1 : 0)
            .scaleEffect(show ? 1 : 0.9)
    }
}

@available(iOS 18.0, *)
struct AppleIntroPage: View {
    @State private var activeCard: Card? = cards.first
    @State private var scrollPosition: ScrollPosition = .init()
    @State private var currentScrollOffset: CGFloat = 0
    @State private var timer = Timer.publish(every: 0.01, on: .current, in: .default).autoconnect()
    @State private var initialAnimation: Bool = false
    @State private var scrollPhase: ScrollPhase = .idle
    var action: () -> Void
    var body: some View {
        ZStack {
            AmbientBackground()
                .animation(.easeInOut(duration: 1), value: activeCard)
            
            VStack(spacing: 40) {
                InfiniteScrollView {
                    ForEach(cards) { card in
                        CarouselCardView(card)
                    }
                }
                .scrollIndicators(.hidden)
                .scrollPosition($scrollPosition)
                .scrollClipDisabled()
                .containerRelativeFrame(.vertical) { value, _ in
                    value * 0.45
                }
                .onScrollPhaseChange({ oldPhase, newPhase in
                    scrollPhase = newPhase
                })
                .onScrollGeometryChange(for: CGFloat.self) {
                    $0.contentOffset.x + $0.contentInsets.leading
                } action: { oldValue, newValue in
                    currentScrollOffset = newValue
                    
                    if scrollPhase != .decelerating || scrollPhase != .animating {
                        let activeIndex = Int((currentScrollOffset / 220).rounded()) % cards.count
                        activeCard = cards[activeIndex]
                    }
                }
                .visualEffect { [initialAnimation] content, proxy in
                    content
                        .offset(y: !initialAnimation ? -(proxy.size.height + 200) : 0)
                }
                
                VStack(spacing: 4) {
                    Image("New-Life-Logo")
                        .resizable()
                        .scaledToFill()
                        .frame(width: 80, height: 80)
                        .cornerRadius(12)
                        .microAnimation(delay: 0.2)
                        .padding(.bottom)
                    
                    Text("Welcome to")
                        .fontWeight(.semibold)
                        .foregroundStyle(.white.secondary)
                        .blurOpacityEffect(initialAnimation)
                    
                    Text("New Life Global Church")
                        .font(.largeTitle.bold())
                        .foregroundStyle(.white)
                    
                    Text("Giving your life a meaning")
                        .font(.callout)
                        .multilineTextAlignment(.center)
                        .foregroundStyle(.white.secondary)
                        .blurOpacityEffect(initialAnimation)
                }
                
                Button {
                    timer.upstream.connect().cancel()
                    action()
                } label: {
                    Text("Continue")
                        .fontWeight(.semibold)
                        .foregroundStyle(.black)
                        .padding(.horizontal, 25)
                        .padding(.vertical, 12)
                        .background(.white, in: .capsule)
                }
                .blurOpacityEffect(initialAnimation)

            }
            .safeAreaPadding(15)
        }
        .onReceive(timer) { _ in
            currentScrollOffset += 0.35
            scrollPosition.scrollTo(x: currentScrollOffset)
        }
        .task {
            try? await Task.sleep(for: .seconds(0.35))
            
            withAnimation(.smooth(duration: 0.75, extraBounce: 0)) {
                initialAnimation =  true
            }
        }
    }
    
    @ViewBuilder private func AmbientBackground() -> some View {
        GeometryReader {
            let size = $0.size
            ZStack {
                ForEach(cards) { card in
                    Image(card.image)
                        .resizable()
                        .aspectRatio(contentMode: .fill)
                        .ignoresSafeArea()
                        .frame(width: size.width, height: size.height)
                        .opacity(activeCard?.id == card.id ? 1 : 0)
                }
                
                Rectangle()
                    .fill(Color.black.opacity(0.45))
                    .ignoresSafeArea()
            }
            .compositingGroup()
            .blur(radius: 90, opaque: true)
            .ignoresSafeArea()
        }
    }
    
    @ViewBuilder private func CarouselCardView(_ card: Card) -> some View {
        GeometryReader {
            let size = $0.size
            
            Image(card.image)
                .resizable()
                .aspectRatio(contentMode: .fill)
                .frame(width: size.width, height: size.height)
                .clipShape(.rect(cornerRadius: 20))
                .shadow(color: .black.opacity(0.4), radius: 10, x: 1, y: 0)
        }
        .frame(width: 220)
        .scrollTransition(.interactive.threshold(.centered), axis: .horizontal) { content, phase in
            content
                .offset(y: phase == .identity ? -10 : 0)
                .rotationEffect(.degrees(phase.value * 5), anchor: .bottom)
        }
    }
}

@available(iOS 18.0, *)
struct InfiniteScrollView<Content: View>: View {
    var spacing: CGFloat = 10
    @ViewBuilder var content: Content
    @State private var contentSize: CGSize = .zero
    
    var body: some View {
        GeometryReader {
            let size = $0.size
            
            ScrollView(.horizontal) {
                HStack(spacing: spacing) {
                    Group(subviews: content) { collection in
                        ///origional content
                        ///
                        HStack(spacing: spacing) {
                            ForEach(collection) { view in
                                view
                            }
                        }
                        .onGeometryChange(for: CGSize.self) {
                            $0.size
                        } action: { newValue in
                            contentSize = .init(width: newValue.width + spacing, height: newValue.height)
                        }
                        
                        ///repeating contnet for creating infiinite(looping) scrollview
                        let averageWidth = contentSize.width / CGFloat(collection.count)
                        let repeatingCount = contentSize.width > 0 ? Int((size.width / averageWidth).rounded()) + 1 : 1
                        
                        HStack(spacing: spacing) {
                            ForEach(0..<repeatingCount, id: \.self) { index in
                                let view = Array(collection)[index % collection.count]
                                
                                view
                            }
                        }

                    }
                }
                .background(InfiniteScrollHelper(contentSize: $contentSize, decelerationRate: .constant(.fast)))
            }
        }
    }
}

fileprivate struct InfiniteScrollHelper: UIViewRepresentable {
    @Binding var contentSize: CGSize
    @Binding var decelerationRate: UIScrollView.DecelerationRate
    
    func makeCoordinator() -> Coordinator {
        Coordinator(decelerationRate: decelerationRate, contentSize: contentSize)
    }
    
    func makeUIView(context: Context) -> UIView {
        let view = UIView(frame: .zero)
        view.backgroundColor = .clear
        
        DispatchQueue.main.async {
            if let scrollView = view.scrollView {
                context.coordinator.defaultDelegate = scrollView.delegate
                scrollView.decelerationRate = decelerationRate
                scrollView.delegate = context.coordinator
            }
        }
        
        return view
    }
    
    func updateUIView(_ uiView: UIView, context: Context) {
        context.coordinator.decelerationRate = decelerationRate
        context.coordinator.contentSize = contentSize
    }
    
    class Coordinator: NSObject, UIScrollViewDelegate {
        var decelerationRate: UIScrollView.DecelerationRate
        var contentSize: CGSize
        
        init(decelerationRate: UIScrollView.DecelerationRate, contentSize: CGSize) {
            self.decelerationRate = decelerationRate
            self.contentSize = contentSize
        }
        
        //storing default swiftui delegate
        weak var defaultDelegate: UIScrollViewDelegate?
        
        func scrollViewDidScroll(_ scrollView: UIScrollView) {
            scrollView.decelerationRate = decelerationRate
            let minX = scrollView.contentOffset.x
            
            if minX > contentSize.width {
                scrollView.contentOffset.x -= contentSize.width
            }
            
            if minX < 0 {
                scrollView.contentOffset.x += contentSize.width
            }
            
            //calling default delegate once our customization finishes
            //to access swiftui's onscrollgeometry, onscrollphasechange and scrllltransition modifiers, we need to activate the corrensponding default delegate callbakcs. I discovered that these 4 callbacks are sufficients to ensure proper functionality when custommizing the swiftui scrolview delegate methods
            defaultDelegate?.scrollViewDidScroll?(scrollView)
        }
        
        //calling other default callbacks
        func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
            defaultDelegate?.scrollViewDidEndDragging?(scrollView, willDecelerate: decelerate)
        }
        
        func scrollViewDidEndDecelerating(_ scrollView: UIScrollView) {
            defaultDelegate?.scrollViewDidEndDecelerating?(scrollView)
        }
        
        func scrollViewWillBeginDragging(_ scrollView: UIScrollView) {
            defaultDelegate?.scrollViewWillBeginDragging?(scrollView)
        }
        
        func scrollViewWillEndDragging(_ scrollView: UIScrollView, withVelocity velocity: CGPoint, targetContentOffset: UnsafeMutablePointer<CGPoint>) {
            defaultDelegate?.scrollViewWillEndDragging?(scrollView, withVelocity: velocity, targetContentOffset: targetContentOffset)
        }
    }
}

extension UIView {
    var scrollView: UIScrollView? {
        if let superview, superview is UIScrollView {
            return superview as? UIScrollView
        }
        
        return superview?.scrollView
    }
}

//MARK: ---------------------------------------------------------------------------------------------iOS17Onboarding

//MARK: -------------------------------------------------------------------LocationContent


//MARK: —————————————————————————————DOTView && NumberDotView

//MARK: ------------------------------------------------ GradientContent



//MARK: ——————————————————————————-MAP-----------------------------------------



//MARK: --------------------------------------------------------------------------------------------- LaunchContent

struct LaunchContentView: View {
    var body: some View {
        /*@START_MENU_TOKEN@*//*@PLACEHOLDER=Hello, world!@*/Text("Hello, world!")/*@END_MENU_TOKEN@*/
    }
}

struct LaunchScreen<RootView: View, Logo: View>: Scene { //should add logo in 1x, 2x and 3x in assets
    var config: LaunchScreenConfig = .init()
    @ViewBuilder var logo: () -> Logo
    @ViewBuilder var rootContent: RootView
    
    var body: some Scene {
        WindowGroup {
            rootContent
                .modifier(LaunchScreenModifier(config: config, logo: logo))
        }
    }
}

fileprivate struct LaunchScreenModifier<Logo: View>: ViewModifier {
    var config: LaunchScreenConfig
    @ViewBuilder var logo: Logo
    @Environment(\.scenePhase) private var scenePhase
    @State private var splashWindow: UIWindow?
    
    func body(content: Content) -> some View {
        content
        //adding an overlay window so that the splash screen will be visible on top of the entire swiftui app
            .onAppear {
                let scenes = UIApplication.shared.connectedScenes
                
                for scene in scenes {
                    guard let windowScene = scene as? UIWindowScene, checkStates(windowScene.activationState),
                          //checking if the window already has a splash screen
                          !windowScene.windows.contains(where: { $0.tag == 1009 }) else {
                        print("We already have a splash screen for this scene")
                        continue
                    }
                    
                    let window = UIWindow(windowScene: windowScene)
                    window.backgroundColor = .clear
                    window.isHidden = false
                    window.isUserInteractionEnabled = true
                    if #available(iOS 17.0, *) {
                        let rootViewController = UIHostingController(rootView: LaunchScreenView(config: config) {
                            logo
                        } isCompleted: {
                            //hiding splash view
                            window.isHidden = true
                            window.isUserInteractionEnabled = false
                        })
                        window.tag = 1009
                        rootViewController.view.backgroundColor = .clear
                        window.rootViewController = rootViewController
                    } else {
                        // Fallback on earlier versions
                        let rootViewController = UIHostingController(rootView: iOS16LaunchScreenView(config: config) {
                            logo
                        } isCompleted: {
                            //hiding splash view
                            window.isHidden = true
                            window.isUserInteractionEnabled = false
                        })
                        window.tag = 1009
                        rootViewController.view.backgroundColor = .clear
                        window.rootViewController = rootViewController
                    }
                    
                    
                    self.splashWindow = window
                    print("splash window added")
                }
            }
    }
    
    ///checking if both the scenephase and windowscen activation are the same
    private func checkStates(_ state: UIWindowScene.ActivationState) -> Bool {
        switch scenePhase {
            case .active: return state == .foregroundActive
            case .inactive: return state == .foregroundInactive
            case .background: return state == .background
            default: return state.hashValue == scenePhase.hashValue
        }
    }
}

///LaunchScreen Configuration for more customization
struct LaunchScreenConfig {
    var intitialDelay: Double = 0.35
    var backgroundColor: Color = .black
    var logoBackgroundColor: Color = .white
    var scaling: CGFloat = 4
    var forcedHideLogo: Bool = false
    var animation: Animation = .smooth(duration: 1, extraBounce: 0)
}

@available(iOS 17.0, *)
fileprivate struct LaunchScreenView<Logo: View>: View {
    var config: LaunchScreenConfig
    @ViewBuilder var logo: Logo
    var isCompleted: () -> ()
    @State private var scaleDown: Bool = false
    @State private var scaleUp: Bool = false
    var body: some View {
        Rectangle()
            .fill(config.backgroundColor)
        //reverse logo masking
            .mask {
                GeometryReader {
                    let size = $0.size.applying(.init(scaleX: config.scaling, y: config.scaling))
                    Rectangle()
                        .overlay {
                            logo
                                .blur(radius: config.forcedHideLogo ? 0 : (scaleUp ? 15 : 0))
                                .blendMode(.destinationOut)
                                .animation(.smooth(duration: 0.3, extraBounce: 0)) { content in
                                    content
                                        .scaleEffect(scaleDown ? 0.8 : 1)
                                }
                                .visualEffect { [scaleUp] content, proxy in
                                    let scaleX: CGFloat = size.width / proxy.size.width
                                    let scaleY: CGFloat = size.height / proxy.size.height
                                    //logo based scaling
                                    let maxScale = Swift.max(scaleX, scaleY)
                                    
                                    return content
                                        .scaleEffect(scaleUp ? maxScale : 1)
                                }
                        }
                }
            }
            .opacity(config.forcedHideLogo ? 1 : (scaleUp ? 0 : 1))
            .background {
                Rectangle()
                    .fill(config.logoBackgroundColor)
                    .opacity(scaleUp ? 0 : 1) //hiding background gradually as logo is scaling up
            }
            .ignoresSafeArea()
            .task {
                guard !scaleDown else { return }
                try? await Task.sleep(for: .seconds(config.intitialDelay))
                scaleDown = true
                try? await Task.sleep(for: .seconds(0.1))
                withAnimation(config.animation, completionCriteria: .logicallyComplete) {
                    scaleUp = true
                } completion: {
                    isCompleted()
                }
                
            }
    }
}

fileprivate struct iOS16LaunchScreenView<Logo: View>: View {
    var config: LaunchScreenConfig
    @ViewBuilder var logo: Logo
    var isCompleted: () -> ()
    
    @State private var scaleDown: Bool = false
    @State private var scaleUp: Bool = false
    
    var body: some View {
        Rectangle()
            .fill(config.backgroundColor)
            .mask {
                GeometryReader { containerProxy in
                    let containerSize = containerProxy.size
                    let scaledSize = containerSize.applying(.init(scaleX: config.scaling, y: config.scaling))
                    
                    Rectangle()
                        .overlay {
                            GeometryReader { logoProxy in
                                let logoSize = logoProxy.size
                                let scaleX = scaledSize.width / max(logoSize.width, 1)
                                let scaleY = scaledSize.height / max(logoSize.height, 1)
                                let maxScale = max(scaleX, scaleY)
                                
                                logo
                                    .blur(radius: config.forcedHideLogo ? 0 : (scaleUp ? 15 : 0))
                                    .blendMode(.destinationOut)
                                    .scaleEffect(scaleDown ? 0.8 : 1)
                                // Use standard animation modifier for iOS 16
                                    .animation(.spring(response: 0.3, dampingFraction: 1.0), value: scaleDown)
                                    .scaleEffect(scaleUp ? maxScale : 1)
                                    .position(x: logoSize.width / 2, y: logoSize.height / 2)
                            }
                        }
                }
            }
            .opacity(config.forcedHideLogo ? 1 : (scaleUp ? 0 : 1))
            .background {
                Rectangle()
                    .fill(config.logoBackgroundColor)
                    .opacity(scaleUp ? 0 : 1)
            }
            .ignoresSafeArea()
            .task {
                guard !scaleDown else { return }
                
                // Task.sleep(for:) is actually available in iOS 16.0+
                // It is much cleaner than nanoseconds calculations
                try? await Task.sleep(for: .seconds(config.intitialDelay))
                
                withAnimation {
                    scaleDown = true
                }
                
                try? await Task.sleep(for: .seconds(0.1))
                
                withAnimation(config.animation) {
                    scaleUp = true
                }
                
                // MATCH the sleep duration to your config.animation duration
                // Added 0.1s buffer to ensure it feels "complete"
                try? await Task.sleep(for: .seconds(1.1))
                
                isCompleted()
            }
    }
}

//we will set up the lgog and background for the default Launch Screen in the app's info.plist. This is to ensure the launch animatio feels continous instead of showing a plain white or back background before the animation starts

//MARK: -----------------------------------------SpriteKit



//MARK: ------------------------------
struct BlendingModesView: View {
    @State var position: CGSize = .zero
    var textParameter: String?
    
    var body: some View {
        ZStack {
            wallpaper
                .overlay(content: {
                    VisualEffectBlur(blurStyle: .systemUltraThinMaterialDark)
                        .opacity(0.7)
                        .ignoresSafeArea()
                })
            
            ZStack {
                text.foregroundColor(.white)
                    .blendMode(.difference)
                    .overlay(text.blendMode(.hue))
                    .overlay(text.foregroundColor(.white).blendMode(.overlay))
                    .overlay(text.foregroundColor(.black).blendMode(.overlay))
            }
        }
    }
    
    var text: some View {
        Text(textParameter ?? "New Life Global")
            .font(.system(size: 48, weight: .heavy))
            .bold()
            .multilineTextAlignment(.center)
            .lineLimit(2)
            .padding(20)
            .frame(width: 390)
            .opacity(0.9)
    }
    
    var wallpaper: some View {
        Image("p1")
            .resizable()
            .aspectRatio(contentMode: .fill)
            .ignoresSafeArea()
            .offset(x: position.width, y: position.height)
            .gesture(
                DragGesture()
                    .onChanged { value in
                        position = value.translation
                    }
                    .onEnded { value in
                        withAnimation {
                            position = .zero
                        }
                    }
            )
    }
}

extension Font {
    static func system(
        size: CGFloat,
        weight: UIFont.Weight,
        width: UIFont.Width) -> Font {
            if #available(iOS 16.0, *) {
                return Font(
                    UIFont.systemFont(
                        ofSize: size,
                        weight: weight,
                        width: width)
                )
            } else {
                return Font(
                    UIFont.systemFont(ofSize: size, weight: weight)
                )
            }
        }
}

var text: some View {
    Text("New Life Global")
        .font(.system(size: 48, weight: .heavy))
        .bold()
        .multilineTextAlignment(.center)
        .lineLimit(2)
        .padding(20)
        .frame(width: 390)
        .opacity(0.1)
}

var wallpaper: some View {
    Image("p1")
        .resizable()
        .aspectRatio(contentMode: .fill)
        .ignoresSafeArea()
}

extension View {
    func darkNeonStyle<S: Shape>(padding: CGFloat = .small, shape: S = Capsule(), colors: [Color] = [Color("lightStart")]) -> some View {
        self.modifier(DarkNeonStyle(padding: padding, shape: shape, colors: colors))
    }
}

struct NeonBackground<S: Shape>: View {
    var shape: S
    var colors: [Color] = [Color("lightStart")]
    
    var body: some View {
        ZStack {
            shape
                .fill(LinearGradient(mycolors: Color.clear, Color.clear))
                .overlay(shape.stroke(RadialGradient(
                    gradient: Gradient(colors: colors),
                    center: .center,
                    startRadius: 20,
                    endRadius: 100
                ),lineWidth: 4))
                .shadow(color: Color("darkStart"), radius: 10, x: -10, y: -10)
                .shadow(color: Color("darkEnd"), radius: 10, x: 10, y: 10)
        }
    }
}

struct DarkNeonStyle<S: Shape>: ViewModifier {
    let padding: CGFloat
    let shape: S
    var colors: [Color]
    
    init(padding: CGFloat = .small, shape: S = Capsule(), colors: [Color] = [Color("lightStart")]) {
        self.padding = padding
        self.shape = shape
        self.colors = colors
    }
    
    func body(content: Content) -> some View {
        content
            .padding(padding)
            .contentShape(shape)
            .background(
                NeonBackground(shape: shape, colors: colors)
            )
    }
}

struct CircleButton: View {
    @State var image: String = "arrow.left"
    var frameWidth: CGFloat = 44
    var frameHeight: CGFloat = 44
    var firstPaletteColor: Color?
    var secondPaletteColor: Color?
    var thirdPaletteColor: Color?
    @State var action: () ->Void
    
    var selectedBorderColors: [Color] = [Color("pink"), Color("pink").opacity(0), Color("pink").opacity(0)]
    
    var body: some View {
        Button {
            action()
        } label: {
            Image(systemName: image)
                .font(.system(size: frameWidth * 0.5))
                .frame(width: frameWidth, height: frameHeight)
                .symbolRenderingMode(.palette)
                .foregroundStyle(firstPaletteColor ?? .white, secondPaletteColor ?? .white, thirdPaletteColor ?? .white)
                .background(LinearGradient(colors: [Color("majenta"), Color("purple")], startPoint: .topLeading, endPoint: .bottomTrailing))
                .cornerRadius(30)
                .overlay(
                    Circle()
                        .trim(from: 0, to: CGFloat(0.5))
                        .stroke(LinearGradient(colors: selectedBorderColors, startPoint: .topLeading, endPoint: .bottomTrailing), style: StrokeStyle(lineWidth: 2))
                        .rotationEffect(.degrees(135))
                        .frame(width: 42, height: 42)
                )
        }
    }
}

//use Flexible Liquid Glass Morphing Tab Bar to Bottom Bar Using SwiftUI | iOS 26 | Xcode 26 when someone clicks on an image in honeybird to review or something like that


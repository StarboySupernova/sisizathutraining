
// MARK: - Main View
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
                        AnnouncementsHeaderView(
                            viewModel: announcementCarouselVM,
                            showAddAnnouncement: $showAddAnnouncement
                        )
                        
                        if #available(iOS 18.0, *) {
                            // Carousel
                            ScrollView(.horizontal, showsIndicators: false) {
                                AnnouncementCarouselView(viewModel: announcementCarouselVM, animation: animation)
                            }
                        } else {
                            AnnouncementCarouselView(viewModel: announcementCarouselVM, animation: animation)
                        }
                        
                        UpcomingEventsHeaderView(
                            viewModel: upcomingEventsCarouselVM
                        )
                        
                        if #available(iOS 18.0, *) {
                            ScrollView(.horizontal, showsIndicators: false) {
                                UpcomingEventCarouselView(viewModel: upcomingEventsCarouselVM, animation: animation2)
                            }
                        } else {
                            UpcomingEventCarouselView(viewModel: upcomingEventsCarouselVM, animation: animation2)
                        }
                        
                        Spacer(minLength: getRect().height * 0.1)
                    }
                }
                .background(ChurchHomeBackgroundView())
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
                DummySheetContentView()
            } bottomBar: { progress in
                BottombarView()
                    .padding(.bottom, 10)
            }
        } else {
            // Fallback on earlier versions
        }
    }
}

// MARK: - Header Views
struct AnnouncementsHeaderView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var viewModel: AnnouncementCarouselViewModel
    @Binding var showAddAnnouncement: Bool
    
    var body: some View {
        HStack {
            GradientText(text: "Announcements")
                .padding(.leading)
            
            Spacer()
            
            if viewModel.swipedAnnouncement > 0 {
                Button {
                    resetViews()
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
    }
    
    private func resetViews() {
        for index in viewModel.announcements.indices {
            withAnimation(.spring()) {
                viewModel.announcements[index].offset = 0
                viewModel.swipedAnnouncement = 0
            }
        }
    }
}

struct UpcomingEventsHeaderView: View {
    @ObservedObject var viewModel: UpcomingEventsCarouselViewModel
    
    var body: some View {
        HStack {
            GradientText(text: "Upcoming Events")
                .foregroundColor(.black)
                .padding(.leading)
            
            Spacer()
            
            if viewModel.swipedEvent > 0 {
                Button {
                    resetViews()
                } label: {
                    Image(systemName: "arrow.counterclockwise")
                        .font(.system(size: 20, weight: .semibold))
                        .foregroundColor(.gray)
                }
                .padding(.trailing)
            }
        }
        .padding()
    }
    
    private func resetViews() {
        for index in viewModel.events.indices {
            withAnimation(.spring()) {
                viewModel.events[index].offset = 0
                viewModel.swipedEvent = 0
            }
        }
    }
}

// MARK: - Carousel Subviews
struct AnnouncementCarouselView: View {
    @ObservedObject var viewModel: AnnouncementCarouselViewModel
    var animation: Namespace.ID
    
    // 1. Explicitly define our screen width for safe calculation
    private let screenWidth = UIScreen.main.bounds.width
    
    var body: some View {
        ZStack {
            ForEach(viewModel.announcements.indices.reversed(), id: \.self) { index in
                HStack {
                    AnnouncementCardView(announcement: viewModel.announcements[index], animation: animation)
                        .frame(
                            width: getCardWidth(),
                            height: getCardHeight(index: index)
                        )
                        .offset(x: getCardOffset(index: index))
                        .rotationEffect(.init(degrees: getCardRotation(index: index)))
                        .environmentObject(viewModel)
                    
                    Spacer(minLength: 0)
                }
                .frame(height: 400)
                .contentShape(Rectangle())
                .offset(x: viewModel.announcements[index].offset)
                .gesture(
                    DragGesture(minimumDistance: 10)
                        .onChanged { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onChanged(value: value, index: index)
                            }
                        }
                        .onEnded { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onEnd(value: value, index: index)
                            }
                        }
                )
            }
        }
        .padding(.top, 25)
        .padding(.horizontal, 30)
    }
    
    private func getCardRotation(index: Int) -> Double {
        let boxWidth = Double(screenWidth / 3)
        let offset = Double(viewModel.announcements[index].offset)
        let angle: Double = 5
        return (offset / boxWidth) * angle
    }
    
    private func getCardHeight(index: Int) -> CGFloat {
        let height: CGFloat = 400
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedAnnouncement)
        let cardHeight = safeIndex <= 2 ? CGFloat(safeIndex) * 35 : 70
        return height - cardHeight
    }
    
    private func getCardWidth() -> CGFloat {
        return screenWidth - 120
    }
    
    private func getCardOffset(index: Int) -> CGFloat {
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedAnnouncement)
        return safeIndex <= 2 ? CGFloat(safeIndex) * 30 : 60
    }
    
    private func onChanged(value: DragGesture.Value, index: Int) {
        // 2. Prevent dragging background cards
        guard index == viewModel.swipedAnnouncement else { return }
        
        if value.translation.width < 0 {
            viewModel.announcements[index].offset = value.translation.width
        }
    }
    
    private func onEnd(value: DragGesture.Value, index: Int) {
        // 2. Prevent ending drag on background cards
        guard index == viewModel.swipedAnnouncement else { return }
        
        withAnimation {
            if -value.translation.width > screenWidth / 3 && viewModel.swipedAnnouncement < viewModel.announcements.count - 1 {
                viewModel.announcements[index].offset = -screenWidth
                viewModel.swipedAnnouncement += 1
            } else {
                viewModel.announcements[index].offset = 0
            }
        }
    }
}

struct UpcomingEventCarouselView: View {
    @ObservedObject var viewModel: UpcomingEventsCarouselViewModel
    var animation: Namespace.ID
    
    // 1. Explicitly define our screen width for safe calculation
    private let screenWidth = UIScreen.main.bounds.width
    
    var body: some View {
        ZStack {
            ForEach(viewModel.events.indices.reversed(), id: \.self) { index in
                HStack {
                    UpcomingEventCardView(event: viewModel.events[index], animation: animation)
                        .frame(
                            width: getCardWidth(),
                            height: getCardHeight(index: index)
                        )
                        .offset(x: getCardOffset(index: index))
                        .rotationEffect(.init(degrees: getCardRotation(index: index)))
                        .environmentObject(viewModel)
                    
                    Spacer(minLength: 0)
                }
                .frame(height: 400)
                .contentShape(Rectangle())
                .offset(x: viewModel.events[index].offset)
                .gesture(
                    DragGesture(minimumDistance: 10)
                        .onChanged { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onChanged(value: value, index: index)
                            }
                        }
                        .onEnded { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onEnd(value: value, index: index)
                            }
                        }
                )
            }
        }
        .padding(.top, 25)
        .padding(.horizontal, 30)
    }
    
    private func getCardRotation(index: Int) -> Double {
        let boxWidth = Double(screenWidth / 3)
        let offset = Double(viewModel.events[index].offset)
        let angle: Double = 5
        return (offset / boxWidth) * angle
    }
    
    private func getCardHeight(index: Int) -> CGFloat {
        let height: CGFloat = 400
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedEvent)
        let cardHeight = safeIndex <= 2 ? CGFloat(safeIndex) * 35 : 70
        return height - cardHeight
    }
    
    private func getCardWidth() -> CGFloat {
        return screenWidth - 120
    }
    
    private func getCardOffset(index: Int) -> CGFloat {
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedEvent)
        return safeIndex <= 2 ? CGFloat(safeIndex) * 30 : 60
    }
    
    private func onChanged(value: DragGesture.Value, index: Int) {
        // 2. Prevent dragging background cards
        guard index == viewModel.swipedEvent else { return }
        
        if value.translation.width < 0 {
            viewModel.events[index].offset = value.translation.width
        }
    }
    
    private func onEnd(value: DragGesture.Value, index: Int) {
        // 2. Prevent ending drag on background cards
        guard index == viewModel.swipedEvent else { return }
        
        withAnimation {
            if -value.translation.width > screenWidth / 3 && viewModel.swipedEvent < viewModel.events.count - 1 {
                viewModel.events[index].offset = -screenWidth
                viewModel.swipedEvent += 1
            } else {
                viewModel.events[index].offset = 0
            }
        }
    }
}

// MARK: - Background & Minor Components
struct ChurchHomeBackgroundView: View {
    var body: some View {
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
    }
}

struct DummySheetContentView: View {
    var body: some View {
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
}

struct BottombarView: View {
    var body: some View {
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